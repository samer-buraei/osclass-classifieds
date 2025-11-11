import os
import sys
import threading
import subprocess
import time
import queue
import json
import tkinter as tk
from tkinter import ttk, filedialog, messagebox

REPO_ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))
RUN_CRAWLER_BAT = os.path.join(REPO_ROOT, 'scripts', 'crawlers', 'halooglasi', 'run_crawler.bat')
LOGIN_HELPER_BAT = os.path.join(REPO_ROOT, 'scripts', 'gui', 'login_via_browser.bat')

class CrawlerGUI(tk.Tk):
    def __init__(self):
        super().__init__()
        self.title('Halooglasi Crawler GUI')
        self.geometry('1200x800')
        self.proc = None
        self.log_queue = queue.Queue()
        self.output_dir = os.path.join(REPO_ROOT, 'scripts', 'crawlers', 'halooglasi', 'output')
        self._build_layout()
        self._start_log_poller()

    def _build_layout(self):
        frm = ttk.LabelFrame(self, text='Controls')
        frm.pack(fill=tk.X, padx=8, pady=8)

        ttk.Label(frm, text='Seed URL:').grid(row=0, column=0, sticky='w', padx=4, pady=4)
        self.seed_var = tk.StringVar(value='https://www.halooglasi.com/')
        ttk.Entry(frm, textvariable=self.seed_var, width=60).grid(row=0, column=1, columnspan=6, sticky='we', padx=4)

        ttk.Label(frm, text='Output Dir:').grid(row=1, column=0, sticky='w', padx=4, pady=4)
        self.output_var = tk.StringVar(value=self.output_dir)
        ttk.Entry(frm, textvariable=self.output_var, width=70).grid(row=1, column=1, columnspan=5, sticky='we', padx=4)
        ttk.Button(frm, text='Browse…', command=self.on_browse_output).grid(row=1, column=6, sticky='we', padx=4)

        ttk.Label(frm, text='Max Pages:').grid(row=2, column=0, sticky='w', padx=4, pady=4)
        self.max_pages_var = tk.StringVar(value='400')
        ttk.Entry(frm, textvariable=self.max_pages_var, width=10).grid(row=2, column=1, sticky='w', padx=4)

        ttk.Label(frm, text='Delay (s):').grid(row=2, column=2, sticky='w', padx=4, pady=4)
        self.delay_var = tk.StringVar(value='1.5')
        ttk.Entry(frm, textvariable=self.delay_var, width=10).grid(row=2, column=3, sticky='w', padx=4)

        ttk.Label(frm, text='Max Depth:').grid(row=2, column=4, sticky='w', padx=4, pady=4)
        self.depth_var = tk.StringVar(value='1')
        ttk.Entry(frm, textvariable=self.depth_var, width=10).grid(row=2, column=5, sticky='w', padx=4)

        self.verbose_var = tk.BooleanVar(value=False)
        self.discover_var = tk.BooleanVar(value=True)
        self.edges_var = tk.BooleanVar(value=False)
        ttk.Checkbutton(frm, text='Verbose', variable=self.verbose_var).grid(row=3, column=0, sticky='w', padx=4)
        ttk.Checkbutton(frm, text='Log discoveries', variable=self.discover_var).grid(row=3, column=1, sticky='w', padx=4)
        ttk.Checkbutton(frm, text='Export edges', variable=self.edges_var).grid(row=3, column=2, sticky='w', padx=4)

        btn_start = ttk.Button(frm, text='Start', command=self.on_start)
        btn_stop = ttk.Button(frm, text='Stop', command=self.on_stop)
        btn_resume = ttk.Button(frm, text='Resume', command=self.on_resume)
        btn_open = ttk.Button(frm, text='Open Output Folder', command=self.on_open_output)
        btn_reload = ttk.Button(frm, text='Reload Views', command=self.reload_views)
        btn_login = ttk.Button(frm, text='Login (browser)', command=self.on_login)
        btn_start.grid(row=4, column=0, padx=4, pady=6, sticky='we')
        btn_stop.grid(row=4, column=1, padx=4, pady=6, sticky='we')
        btn_resume.grid(row=4, column=2, padx=4, pady=6, sticky='we')
        btn_open.grid(row=4, column=3, padx=4, pady=6, sticky='we')
        btn_reload.grid(row=4, column=4, padx=4, pady=6, sticky='we')
        btn_login.grid(row=4, column=5, padx=4, pady=6, sticky='we')

        for i in range(7):
            frm.columnconfigure(i, weight=1)

        mid = ttk.Panedwindow(self, orient=tk.HORIZONTAL)
        mid.pack(fill=tk.BOTH, expand=True, padx=8, pady=4)

        logs_frame = ttk.LabelFrame(mid, text='Live Logs')
        self.log_text = tk.Text(logs_frame, height=20, wrap='none')
        self.log_text.pack(fill=tk.BOTH, expand=True)
        mid.add(logs_frame, weight=2)

        right_nb = ttk.Notebook(self)
        mid.add(right_nb, weight=2)

        self.tree_tab = ttk.Frame(right_nb)
        right_nb.add(self.tree_tab, text='Hierarchy')
        self.tree = ttk.Treeview(self.tree_tab)
        self.tree.pack(fill=tk.BOTH, expand=True)

        self.links_tab = ttk.Frame(right_nb)
        right_nb.add(self.links_tab, text='Links')
        self.links_list = tk.Listbox(self.links_tab)
        self.links_list.pack(fill=tk.BOTH, expand=True)

        status = ttk.Frame(self)
        status.pack(fill=tk.X, padx=8, pady=4)
        self.status_var = tk.StringVar(value='Idle')
        ttk.Label(status, textvariable=self.status_var).pack(side=tk.LEFT)

    def on_browse_output(self):
        path = filedialog.askdirectory(initialdir=self.output_var.get() or self.output_dir)
        if path:
            self.output_var.set(path)
            self.output_dir = path

    def on_open_output(self):
        try:
            os.makedirs(self.output_dir, exist_ok=True)
            if sys.platform.startswith('win'):
                os.startfile(self.output_dir)
            else:
                subprocess.Popen(['xdg-open', self.output_dir])
        except Exception as e:
            messagebox.showerror('Error', str(e))

    def _cookies_file(self) -> str:
        return os.path.join(self.output_var.get().strip(), 'cookies.json')

    def _build_args(self, resume=False):
        args = [RUN_CRAWLER_BAT]
        args += ['--output-dir', self.output_var.get().strip()]
        args += ['--checkpoint-path', os.path.join(self.output_var.get().strip(), 'checkpoint.json')]
        cookies = self._cookies_file()
        if os.path.exists(cookies):
            args += ['--cookies-file', cookies]
        if self.seed_var.get().strip():
            args += ['--seed', self.seed_var.get().strip()]
        if self.max_pages_var.get().strip():
            args += ['--max-pages', self.max_pages_var.get().strip()]
        if self.delay_var.get().strip():
            args += ['--delay-seconds', self.delay_var.get().strip()]
        if self.depth_var.get().strip():
            args += ['--max-depth', self.depth_var.get().strip()]
        if self.verbose_var.get():
            args += ['--verbose']
        if self.discover_var.get():
            args += ['--log-discoveries']
        if self.edges_var.get():
            args += ['--export-edges']
        if resume:
            args += ['--resume']
        args += ['--checkpoint-every', '1', '--progress-every', '5']
        return args

    def _reader_thread(self, proc):
        try:
            for line in iter(proc.stdout.readline, b''):
                try:
                    self.log_queue.put(line.decode(errors='replace'))
                except Exception:
                    pass
        finally:
            self.log_queue.put('[info] process exited\n')

    def _start_proc(self, args):
        cwd = REPO_ROOT
        self.proc = subprocess.Popen(
            args,
            cwd=cwd,
            stdout=subprocess.PIPE,
            stderr=subprocess.STDOUT,
            bufsize=1
        )
        t = threading.Thread(target=self._reader_thread, args=(self.proc,), daemon=True)
        t.start()

    def on_start(self):
        if self.proc and self.proc.poll() is None:
            messagebox.showinfo('Running', 'Crawler is already running')
            return
        args = self._build_args(resume=False)
        self.log_text.delete('1.0', tk.END)
        self.status_var.set('Starting …')
        self._start_proc(args)

    def on_resume(self):
        if self.proc and self.proc.poll() is None:
            messagebox.showinfo('Running', 'Crawler is already running')
            return
        args = self._build_args(resume=True)
        self.log_text.delete('1.0', tk.END)
        self.status_var.set('Resuming …')
        self._start_proc(args)

    def on_stop(self):
        if self.proc and self.proc.poll() is None:
            try:
                pid = self.proc.pid
                # Kill the whole tree on Windows
                if sys.platform.startswith('win'):
                    subprocess.run(["taskkill", "/PID", str(pid), "/T", "/F"], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
                else:
                    self.proc.terminate()
            except Exception:
                pass
            self.status_var.set('Stopping …')
        else:
            self.status_var.set('Idle')

    def on_login(self):
        # Open browser helper to log in and save cookies to selected output dir
        out_dir = self.output_var.get().strip()
        seed = self.seed_var.get().strip()
        if not seed:
            messagebox.showerror('Error', 'Seed URL is required')
            return
        try:
            os.makedirs(out_dir, exist_ok=True)
            args = [LOGIN_HELPER_BAT, '--seed', seed, '--output-dir', out_dir]
            subprocess.Popen(args, cwd=REPO_ROOT)
            messagebox.showinfo('Login', 'Browser login launched. Complete login, return to the console window and press Enter to save cookies. Then press Reload Views or Start to use cookies.')
        except Exception as e:
            messagebox.showerror('Error', str(e))

    def _start_log_poller(self):
        def poll():
            while True:
                try:
                    line = self.log_queue.get_nowait()
                except queue.Empty:
                    break
                self.log_text.insert(tk.END, line)
                self.log_text.see(tk.END)
                if line.startswith('[progress]'):
                    self.status_var.set(line.strip())
                if '[ok] Wrote outputs in' in line:
                    self.status_var.set('Done')
            self.after(150, self._start_log_poller)
        poll()

    def reload_views(self):
        tree_json = os.path.join(self.output_var.get().strip(), 'tree.json')
        tree_txt = os.path.join(self.output_var.get().strip(), 'tree.txt')
        urls_txt = os.path.join(self.output_var.get().strip(), 'urls.txt')
        for i in self.tree.get_children():
            self.tree.delete(i)
        try:
            if os.path.exists(tree_json):
                with open(tree_json, 'r', encoding='utf-8') as f:
                    trie = json.load(f)
                self._populate_tree('/', trie.get('/', {}), '')
            elif os.path.exists(tree_txt):
                with open(tree_txt, 'r', encoding='utf-8') as f:
                    self.log_text.insert(tk.END, '\n[tree]\n' + f.read() + '\n')
        except Exception as e:
            self.log_text.insert(tk.END, f"[error] loading tree: {e}\n")
        self.links_list.delete(0, tk.END)
        try:
            if os.path.exists(urls_txt):
                with open(urls_txt, 'r', encoding='utf-8') as f:
                    for i, line in enumerate(f):
                        if i > 5000:
                            self.links_list.insert(tk.END, '… (truncated)')
                            break
                        self.links_list.insert(tk.END, line.strip())
        except Exception as e:
            self.log_text.insert(tk.END, f"[error] loading links: {e}\n")

    def _populate_tree(self, label, node, parent_id):
        this_id = self.tree.insert(parent_id, 'end', text=label)
        for key, child in sorted(node.items()):
            if key == '_count':
                continue
            self._populate_tree(key, child, this_id)


def main():
    app = CrawlerGUI()
    app.mainloop()

if __name__ == '__main__':
    main()
