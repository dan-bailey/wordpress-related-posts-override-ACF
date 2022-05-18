# wordpress-related-posts-override-ACF
Allows a user to add new "related posts" to an individual post and override the automatic functions built into Wordpress.  This code utilizes the Advanced Custom Fields Pro plugin, and allows the user to pick multiple posts to override the base code that selects four "related" posts.  The site this came from was designed with four related posts in-mind, but the user is not required to select four.  The user-selected posts are added to the beginning of the array holding the automatically-selected posts, and then the array is cut back to four items.

# remaining to-do items
I should probably write some code to ensure that none of the user-selected posts are already in the array, and if they are, then I'll need to excise them, and do so in a way that respects the four-post limit.
