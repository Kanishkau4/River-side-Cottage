MANUAL ROOM IMAGES SETUP FOR LUXEVISTA RESORT
==========================================

This update provides dedicated room detail pages with multiple images for each room. All images are manually managed (not database-driven) to match your preference.

NEW FILES CREATED:
------------------

1. room-deluxe-suite.html
   - Detailed page for the Deluxe River View Suite
   - Shows multiple images in a carousel
   - Includes room description, amenities, and booking form

2. room-garden-view.html
   - Detailed page for the Garden View Room
   - Shows multiple images in a carousel
   - Includes room description, amenities, and booking form

FILES MODIFIED:
---------------

1. rooms.html
   - Updated links to point to the new room detail pages
   - "View Details" and "Book Now" buttons now link to specific room pages

2. rooms-single.html
   - Added redirect to rooms.html since this page is no longer used

HOW TO ADD MORE IMAGES TO ROOMS:
--------------------------------

1. Add your images to the images/ directory
   - Name them descriptively (e.g., room1-balcony.jpg, room1-bathroom.jpg)
   - Use JPG format for best compatibility

2. Edit the room detail pages to include your new images:
   - Open room-deluxe-suite.html or room-garden-view.html
   - Find the image carousel section (single-slider owl-carousel)
   - Add new items following this pattern:
   
   <div class="item">
       <div class="room-img" style="background-image: url(images/your-new-image.jpg);"></div>
   </div>

3. Example of adding a new image:
   ```html
   <div class="single-slider owl-carousel">
       <div class="item">
           <div class="room-img" style="background-image: url(images/room-1.jpg);"></div>
       </div>
       <div class="item">
           <div class="room-img" style="background-image: url(images/room-4.jpg);"></div>
       </div>
       <!-- Add your new image here -->
       <div class="item">
           <div class="room-img" style="background-image: url(images/your-new-image.jpg);"></div>
       </div>
   </div>
   ```

RECOMMENDED IMAGE SIZES:
------------------------
- For best results, use images that are at least 800x600 pixels
- All images in a carousel will be displayed at the same size
- Keep file sizes reasonable for web use (under 500KB each)

IMAGE NAMING CONVENTION:
------------------------
- Use descriptive names: room1-balcony.jpg, room1-bathroom.jpg
- Include the room name or number in the filename
- Use hyphens instead of spaces in filenames

EXISTING ROOM IMAGES YOU CAN USE:
---------------------------------
The template already includes several room images you can use:
- images/room-1.jpg (Deluxe Suite main image)
- images/room-2.jpg (Garden View main image)
- images/room-3.jpg
- images/room-4.jpg
- images/room-5.jpg
- images/room-6.jpg

NAVIGATION:
-----------
- All room detail pages have proper breadcrumbs
- Navigation is consistent with the rest of the site
- Back buttons return to the main rooms page

BOOKING INTEGRATION:
--------------------
- Each room detail page has a booking form
- Forms pre-select the correct room type
- Date pickers are functional
- Form submits to book.php with room information

CUSTOMIZATION:
--------------
You can easily customize:
- Room descriptions
- Amenities lists
- Image carousels
- Pricing information
- All text content

TECHNICAL NOTES:
----------------
- Uses the existing Owl Carousel for image display
- Responsive design that works on all devices
- No database required for images
- All content is in static HTML for easy editing
- Maintains the LuxeVista Resort branding and design

This approach allows you to manually manage all room images while providing a professional, attractive display for your guests.