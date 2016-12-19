Free PHP Gallery by adrianTNT.com
================================= 

- unzip all files from this zip
- edit settings.php file and set your own admin username / password
- upload the files on your server using a FTP program or Adobe Dreamweaver, etc
- access the folder in your browser, e.g www.Example.com/gallery/ 
- you can now click "admin" at the bottom of the page and enter your user/password
- on the admin page you can create 1-2 categories
- visit the gallery main page again, click the categories and then the "Upload photos" button


No database is required for this script, the categories that you create are actual folders on servers, and the name of your photos is taken from the jpg file name.



New in version 1.1
==================

- you can now use special characters in category names and photo names, if you notice problems please contact us
- you can upoad multiple photos by FTP and then go to "admin > regenerate images" in order to create thumbnails for them

- note: the new file name of each photo file was changed like this: landscape.jpg, landscape_small.jpg and landscape_thumb.jpg; names like landscape_source.jpg are not longer used


Known problems:
=============== 

1) The url rewrite doesn't work, e.g /gallery/login shows error but /gallery/login.php works ok
Solution: make sure on server you have the .htaccess in the gallery folder, open .htaccess file and uncomment this line:
RewriteBase /gallery
Replace the path /gallery with your own path if needed




If you need any help contact us at http://www.adriantnt.com/contact/ 