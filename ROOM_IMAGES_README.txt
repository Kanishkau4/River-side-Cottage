ROOM IMAGES MANAGEMENT FOR LUXEVISTA RESORT
=========================================

This update adds support for multiple images per room and a dedicated room details page with booking functionality.

NEW FEATURES:
-------------

1. Room Details Page:
   - Each room now has its own detailed page at room-details.php?id=X
   - Shows multiple images in a carousel
   - Displays room details (description, amenities, price, capacity)
   - Includes a booking form directly on the page

2. Multiple Images Support:
   - Database now supports multiple images per room type
   - Images are stored in the room_images table
   - Each image can have a caption

3. Image Management:
   - Admin panel at admin/manage-images.php for uploading new images
   - Simple interface to associate images with room types

HOW TO ADD ROOM IMAGES:
-----------------------

1. Visit admin/manage-images.php in your browser
2. Select a room type from the dropdown
3. Choose an image file (JPG, PNG, GIF supported)
4. Optionally add a caption
5. Click "Upload Image"

The image will be:
- Uploaded to the images/room-images/ directory
- Added to the database with the correct room type association
- Displayed in the carousel on the room details page

HOW TO VIEW ROOM DETAILS:
-------------------------

1. Visit rooms.html to see the room listings
2. Click "View Details" or "Book Now" for any room
3. You'll be taken to room-details.php?id=X where X is the room type ID
4. The page shows all images for that room in a carousel
5. You can book directly from this page

TECHNICAL CHANGES:
------------------

1. Database:
   - Added room_images table with columns: id, room_type_id, image_url, caption
   - Added sample images for both room types

2. Files:
   - Created room-details.php for individual room pages
   - Updated rooms.html to link to room details pages
   - Created admin/manage-images.php for image management
   - Updated db_schema.sql with new table and sample data
   - Updated includes/booking_handler.php with new functions

3. Navigation:
   - Room details pages have proper breadcrumbs
   - All navigation links maintained consistently

SUPPORTED IMAGE FORMATS:
------------------------
- JPG/JPEG
- PNG
- GIF

RECOMMENDED IMAGE DIMENSIONS:
-----------------------------
- For best results, use images that are at least 800x600 pixels
- All images in a room carousel will be displayed at the same size

NOTE:
-----
This is a template system. For production use, you should:
- Add proper admin authentication
- Implement image resizing for better performance
- Add image deletion functionality
- Implement proper error handling