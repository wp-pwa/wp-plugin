module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    wp_readme_to_markdown: {
      your_target: {
        files: {
          "README.md": "readme.txt"
        }
      }
    }
  })

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks("grunt-wp-readme-to-markdown")

  // Default task(s).
  grunt.registerTask("default", ["wp_readme_to_markdown"])
}
