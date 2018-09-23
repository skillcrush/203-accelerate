<?php
delete_option('blogger_importer');
delete_option('blogger_importer_connector');
$blogopt = true;                    
for ($i = 1; $blogopt ; $i++) {
$blogopt = get_option('blogger_importer_blog_'.$i);
if (is_array($blogopt)) 
	delete_option('blogger_importer_blog_'.$i);
}
