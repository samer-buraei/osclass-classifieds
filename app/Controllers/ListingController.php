<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Security;
use App\Helpers\FileUpload;

class ListingController extends Controller
{
    public function show($id)
    {
        $listingModel = $this->model('Listing');
        $listing = $listingModel->find($id);
        
        if (!$listing || $listing['status'] !== 'active') {
            $this->redirect('404');
        }
        
        // Increment view count
        $listingModel->update($id, ['views' => $listing['views'] + 1]);
        
        // Get seller info
        $userModel = $this->model('User');
        $seller = $userModel->find($listing['user_id']);
        
        // Get images
        $imageModel = $this->model('ListingImage');
        $images = $imageModel->where('listing_id = :id', ['id' => $id]);
        
        // Get similar listings
        $similarListings = $listingModel->where(
            'category_id = :category AND id != :id AND status = :status LIMIT 6',
            ['category' => $listing['category_id'], 'id' => $id, 'status' => 'active']
        );
        
        $data = [
            'title' => $listing['title'],
            'listing' => $listing,
            'seller' => $seller,
            'images' => $images,
            'similar_listings' => $similarListings,
        ];
        
        parent::view('listings/view', $data);
    }

    public function create()
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
        } else {
            $categoryModel = $this->model('Category');
            $locationModel = $this->model('Location');
            
            $data = [
                'title' => 'Create Listing',
                'categories' => $categoryModel->all(),
                'locations' => $locationModel->all(),
            ];
            
            $this->view('listings/create', $data);
        }
    }

    private function handleCreate()
    {
        // Validate CSRF token
        if (!Security::validateCSRF($_POST['csrf_token'] ?? '')) {
            $this->json(['error' => 'Invalid request'], 403);
        }
        
        // Validate input
        $errors = [];
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category_id = intval($_POST['category_id'] ?? 0);
        $location_id = intval($_POST['location_id'] ?? 0);
        
        if (empty($title)) $errors[] = 'Title is required';
        if (empty($description)) $errors[] = 'Description is required';
        if ($category_id <= 0) $errors[] = 'Category is required';
        
        if (!empty($errors)) {
            $this->json(['errors' => $errors], 400);
        }
        
        // Create listing
        $listingModel = $this->model('Listing');
        
        $listingData = [
            'user_id' => $_SESSION['user_id'],
            'category_id' => $category_id,
            'location_id' => $location_id,
            'title' => Security::sanitize($title),
            'description' => Security::sanitize($description),
            'price' => $price,
            'status' => 'pending', // Pending moderation
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        $listingId = $listingModel->create($listingData);
        
        // Handle image uploads
        if (!empty($_FILES['images']['name'][0])) {
            FileUpload::handleListingImages($listingId, $_FILES['images']);
        }
        
        $this->json([
            'success' => true,
            'listing_id' => $listingId,
            'message' => 'Listing created successfully'
        ]);
    }

    public function edit($id)
    {
        $this->requireAuth();
        
        $listingModel = $this->model('Listing');
        $listing = $listingModel->find($id);
        
        if (!$listing || $listing['user_id'] != $_SESSION['user_id']) {
            $this->redirect('404');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEdit($id);
        } else {
            $categoryModel = $this->model('Category');
            $locationModel = $this->model('Location');
            $imageModel = $this->model('ListingImage');
            
            $data = [
                'title' => 'Edit Listing',
                'listing' => $listing,
                'categories' => $categoryModel->all(),
                'locations' => $locationModel->all(),
                'images' => $imageModel->where('listing_id = :id', ['id' => $id]),
            ];
            
            $this->view('listings/edit', $data);
        }
    }

    private function handleEdit($id)
    {
        // Similar validation as create
        $listingModel = $this->model('Listing');
        
        $updateData = [
            'title' => Security::sanitize($_POST['title'] ?? ''),
            'description' => Security::sanitize($_POST['description'] ?? ''),
            'price' => floatval($_POST['price'] ?? 0),
            'category_id' => intval($_POST['category_id'] ?? 0),
            'location_id' => intval($_POST['location_id'] ?? 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        $listingModel->update($id, $updateData);
        
        $this->json(['success' => true, 'message' => 'Listing updated successfully']);
    }

    public function delete($id)
    {
        $this->requireAuth();
        
        $listingModel = $this->model('Listing');
        $listing = $listingModel->find($id);
        
        if ($listing && $listing['user_id'] == $_SESSION['user_id']) {
            $listingModel->delete($id);
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Unauthorized'], 403);
        }
    }
}

