# Hierarchical Categories Management System

## Overview

This Laravel application implements a comprehensive hierarchical categories management system with the following features:

### ✅ Implemented Features

#### 1. Hierarchical Category Display

-   **Tree-like structure** with multiple levels
-   **Arrow symbols (→)** showing hierarchical progression
-   **Collapse/Expand functionality** with arrow icons
-   **Session-based state management** for expand/collapse preferences

#### 2. Category Information Display

-   **Category Name**: Primary text for the category
-   **Direct Children Count**: First-level subcategories
-   **Total Descendants Count**: Sum of all children and grandchildren
-   **Category Status**: Active/Inactive with color distinction
-   **Creation and Modification Dates**: Timestamp information in tooltips

#### 3. Visual Indicators

-   **Downward arrow icon** when subcategories exist
-   **Different color** for inactive categories
-   **Children counter** next to category name
-   **Status badges** (Active/Inactive)

#### 4. Category Operations

-   **Add**: Create new subcategory with smart dropdown
-   **Edit**: Modify current category data
-   **Delete**: Remove category with validation
-   **Drag & Drop**: Reorder categories with visual feedback

#### 5. Bulk Operations

-   **Multi-select** categories using checkboxes
-   **Bulk Move**: Move multiple categories to a single parent
-   **Bulk Delete**: Delete multiple categories with confirmation

#### 6. Adding New Category

-   **Category Name**: Required text field
-   **Order**: Numeric field to set category order
-   **Parent Category**: Smart dropdown with hierarchical display
-   **Category Status**: Active/Inactive (default: Active)

#### 7. Category Editing

-   **Current data** displayed in form
-   **Category Name**: Current value
-   **Parent Category**: Current value with ability to change
-   **Category Status**: Current status
-   **Auto-update** modification date

#### 8. Drag and Drop Feature (Sortable)

-   **Reordering**: Drag categories to change order among siblings
-   **Hierarchical movement**: Drag left to make child, right to move up
-   **Transfer rules**: Validates against circular references
-   **Warning system**: Alerts when moving categories with many children

#### 9. Validation and Confirmations

-   **Delete confirmation**: For categories with children
-   **Subcategory count display**: Shows how many will be deleted
-   **Warning messages**: For operations affecting many subcategories

#### 10. Preference Saving

-   **Session-based storage**: Save expand/collapse state per user
-   **State restoration**: Restore when returning to page
-   **Reset functionality**: Clear all saved states

#### 11. Additional Information Display

-   **Element Counter**: Shows total descendants for each category
-   **Timing Information**: Creation and modification dates in tooltips
-   **User attribution**: Shows who created each category

#### 12. Search and Filtering

-   **Quick search**: In category names
-   **Status filter**: Active/Inactive categories
-   **Level filter**: Filter by hierarchical depth
-   **Real-time results**: Dynamic search with SweetAlert notifications

## Installation and Setup

### 1. Prerequisites

-   PHP 8.2+
-   Laravel 12.0+
-   SQLite (or MySQL/PostgreSQL)
-   Node.js and npm

### 2. Installation Steps

```bash
# Clone the repository
git clone <repository-url>
cd sortable

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed

# Start the development server
php artisan serve
```

### 3. Access the Application

Visit `http://localhost:8000/admin/categories` to access the hierarchical categories management system.

## Sample Data Structure

The system comes with sample hierarchical data:

```
Palestine (Total: 4)
  → Hebron (Total: 2)
    → → Nuba (Total: 1)
      → → → Nuba School (Total: 0)
Jordan (Total: 1)
  → Amman (Total: 0)
Syria (Total: 4)
  → Alepo (Total: 3)
    → → Alepo 2 (Total: 2)
      → → → Alepo 3 (Total: 1)
        → → → → Alepo 4 (Total: 0) [Inactive]
```

## API Endpoints

### Category Management

-   `GET /admin/categories` - Display categories page
-   `GET /admin/categories/hierarchical` - Get hierarchical data for dropdowns
-   `POST /admin/categories` - Create new category
-   `PUT /admin/categories/{id}` - Update category
-   `PUT /admin/categories/{id}/order` - Update category order
-   `DELETE /admin/categories/{id}` - Delete category

### Bulk Operations

-   `POST /admin/categories/bulk-delete` - Bulk delete categories
-   `POST /admin/categories/bulk-move` - Bulk move categories

### Session Management

-   `POST /admin/categories/expand-state` - Save expand/collapse state
-   `GET /admin/categories/expand-state` - Get saved state
-   `DELETE /admin/categories/expand-state` - Reset state

### Search and Filter

-   `GET /admin/categories/search` - Search categories with filters

## Key Features Implementation

### 1. SweetAlert2 Integration

All confirmations and notifications use SweetAlert2 for a modern, user-friendly experience.

### 2. Drag and Drop (SortableJS)

-   Visual feedback during drag operations
-   Automatic order updates
-   Hierarchical movement support
-   Circular reference prevention

### 3. Nice Select Integration

Enhanced dropdown experience for parent category selection with search functionality.

### 4. Session State Management

-   Automatic save of expand/collapse preferences
-   User-specific state storage
-   State restoration on page load

### 5. Responsive Design

-   Bootstrap 5 framework
-   Mobile-friendly interface
-   Clean, modern UI

## Database Schema

### Categories Table (`cats`)

-   `id` - Primary key
-   `parent_id` - Self-referencing foreign key
-   `ord` - Order within siblings
-   `status` - Active/Inactive boolean
-   `user_id` - Creator reference
-   `created_at`, `updated_at` - Timestamps

### Category Translations Table (`cat_translations`)

-   `cat_id` - Foreign key to categories
-   `title` - Category name
-   `locale` - Language code
-   `description`, `seo_keywords`, `seo_description` - Additional fields

## Model Relationships

The `Cat` model includes comprehensive relationship methods:

```php
// Parent-child relationships
$category->parent()           // Get parent category
$category->children()         // Get direct children
$category->childrenRecursive() // Get all descendants

// Utility methods
$category->descendants()      // All descendants
$category->ancestors()        // All ancestors
$category->root()            // Top-level parent
$category->siblings()        // Categories with same parent

// Computed attributes
$category->depth             // Hierarchical level
$category->path              // Full path from root
$category->is_leaf          // Has no children
$category->is_root          // Has no parent
```

## Security Features

-   **CSRF Protection**: All forms include CSRF tokens
-   **Input Validation**: Comprehensive validation rules
-   **Circular Reference Prevention**: Prevents invalid hierarchical structures
-   **Soft Deletes**: Categories are soft-deleted for data integrity

## Performance Optimizations

-   **Eager Loading**: Relationships loaded efficiently
-   **Database Indexing**: Proper indexes on foreign keys
-   **Session Caching**: Expand state cached in session
-   **Lazy Loading**: Categories loaded on demand

## Browser Compatibility

-   **Modern Browsers**: Chrome, Firefox, Safari, Edge
-   **Mobile Support**: Responsive design for tablets and phones
-   **JavaScript Required**: Enhanced functionality requires JavaScript

## Troubleshooting

### Common Issues

1. **Foreign Key Constraint Errors**

    - Ensure users exist before creating categories
    - Check that parent categories exist when creating children

2. **Session Issues**

    - Clear browser cache if expand state doesn't persist
    - Check Laravel session configuration

3. **Drag and Drop Not Working**
    - Ensure JavaScript is enabled
    - Check for console errors
    - Verify SortableJS is loaded

### Debug Mode

Enable debug mode in `.env`:

```
APP_DEBUG=true
```

## Contributing

1. Follow Laravel coding standards
2. Add tests for new features
3. Update documentation
4. Ensure all features work with existing data

## License

This project is licensed under the MIT License.
