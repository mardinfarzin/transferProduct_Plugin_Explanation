# transferProduct Plugin Explanation

## Overview
This WordPress plugin is designed to transfer products from an Excel file to WooCommerce. It provides a custom admin menu with two main pages: one for uploading and transferring product data, and another for managing the settings related to the product meta.

---

## Main Functions

### 1. `transfer_product_page()`
This function creates the primary admin page where users can upload an Excel file and track the upload progress.

- **Upload Button**: 
  - A button that triggers a hidden form to upload the Excel file.
  
- **File Upload Form**: 
  - A form with a file input field where users can select an Excel file and submit it.
  
- **Progress Bar**: 
  - A hidden progress bar that shows the upload progress once the file is being uploaded.

- **Modal for Displaying Uploaded Data**:
  - A modal dialog that opens to display the data after the Excel file is processed.

### 2. `transfer_product_setting_theme()`
This function creates the settings page for managing product meta data.

- **Search Input**: 
  - An input field for filtering or searching through product meta settings.
  
- **Bulk Delete Button**: 
  - A button to delete multiple settings at once.
  
- **Add New Meta**: 
  - A link that opens a modal where users can add new meta fields for the products.
  
- **Table Display**: 
  - A table that displays all the product meta settings from the database.
  
- **Modal for Adding New Meta**: 
  - A modal window that allows the user to input a URL or other metadata values to associate with the products.

---

## How It Works

1. **Menu Creation**: 
   - The plugin adds a custom menu to the WordPress admin panel using the `add_menu_page` and `add_submenu_page` functions.

2. **Upload Excel File**: 
   - Users can upload an Excel file containing product information. The plugin processes the file and displays the data in a modal.

3. **Manage Product Meta**: 
   - Admins can view, add, or delete product meta data (such as product attributes) through a dedicated settings page.

---

## Key Features

- **Excel File Upload**: Supports uploading and processing product data from an Excel file.
- **Progress Bar**: Displays the progress of file uploads in real-time.
- **Product Meta Management**: Allows admins to manage meta data related to products.
- **Responsive Design**: The admin interface is built using Bootstrap classes to ensure responsiveness and a clean UI.

