> ## Decisions, not Options

# Filters

- **ejo_testimonials**
- ejo_testimonials_single
- ejo_testimonials_archive
- ejo_testimonials_widget

Example: 
add_filter('ejo_testimonials', 'ejo_testimonials_custom_output', 10, 9);
function ejo_testimonials_custom_output( $output, $title, $image, $testimonial, $author, $info, $date, $company, $link )
{ return $output; }

## Specifics
- image_size
- title
- author
- testimonial
- info
- date
- read_more
- ...
