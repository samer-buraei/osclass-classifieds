<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $listingModel = $this->model('Listing');
        
        // Get latest listings
        $latestListings = $listingModel->paginate(1, 12, 'status = :status', ['status' => 'active']);
        
        // Get featured listings
        $featuredListings = $listingModel->where('status = :status AND featured = 1', ['status' => 'active']);
        
        // Get categories with listing count
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->all();
        
        $data = [
            'title' => APP_NAME . ' - Buy and Sell Everything',
            'latest_listings' => $latestListings['items'],
            'featured_listings' => $featuredListings,
            'categories' => $categories,
        ];
        
        $this->view('home/index', $data);
    }

    public function search()
    {
        $query = $_GET['q'] ?? '';
        $category = $_GET['category'] ?? '';
        $location = $_GET['location'] ?? '';
        $page = $_GET['page'] ?? 1;
        
        $listingModel = $this->model('Listing');
        
        $where = 'status = :status';
        $params = ['status' => 'active'];
        
        if ($query) {
            $where .= ' AND (title LIKE :query OR description LIKE :query)';
            $params['query'] = "%$query%";
        }
        
        if ($category) {
            $where .= ' AND category_id = :category';
            $params['category'] = $category;
        }
        
        if ($location) {
            $where .= ' AND location_id = :location';
            $params['location'] = $location;
        }
        
        $results = $listingModel->paginate($page, ITEMS_PER_PAGE, $where, $params);
        
        $data = [
            'title' => 'Search Results',
            'listings' => $results['items'],
            'pagination' => $results,
            'query' => $query,
        ];
        
        $this->view('listings/search', $data);
    }
}

