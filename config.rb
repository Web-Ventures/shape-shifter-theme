# Require any additional compass plugins here.
# -----------------------------------------------------------------------------
require "susy";
require 'sassy-buttons'


# Set this to the root of your project when deployed:
# -----------------------------------------------------------------------------

http_path = "/"
css_dir = "library/css"
sass_dir = "library/sass"
images_dir = "library/images"
javascripts_dir = "library/js"
# svg_dir = "library/svg"
# fonts_dir = "library/fonts"
# docs_dir = "library/docs"
# plugins_dir = "library/plugins"



# Output style and comments
# -----------------------------------------------------------------------------

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed
# Over-ride with force compile to change output style with: compass compile --output-style compressed --force
output_style = :expanded


# Remove SASS/Compass relative comments.
line_comments = false



# Stuff we don't really need below
# -----------------------------------------------------------------------------

# To enable relative paths to library via compass helper functions. Uncomment:
# relative_library = true

# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass src scss && rm -rf sass && mv scss sass