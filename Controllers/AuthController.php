<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Security;

class AuthController extends Controller
{
    public function register()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            $this->view('auth/register', ['title' => 'Register']);
        }
    }

    private function handleRegister()
    {
        $errors = [];
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
        if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';
        
        // Check if email exists
        $userModel = $this->model('User');
        if ($userModel->findWhere('email = :email', ['email' => $email])) {
            $errors[] = 'Email already registered';
        }
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }
        
        // Create user
        $userId = $userModel->create([
            'name' => Security::sanitize($name),
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Auto login
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        
        $this->json(['success' => true, 'redirect' => 'dashboard']);
    }

    public function login()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->view('auth/login', ['title' => 'Login']);
        }
    }

    private function handleLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $userModel = $this->model('User');
        $user = $userModel->findWhere('email = :email', ['email' => $email]);
        
        if (!$user || !password_verify($password, $user['password'])) {
            $this->json(['error' => 'Invalid email or password'], 401);
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        $this->json(['success' => true, 'redirect' => 'dashboard']);
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('');
    }
}

