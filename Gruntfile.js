module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    wp_readme_to_markdown: {
      your_target: {
        files: {
          "README.md": "readme.txt"
        }
      }
    },
    uglify: {
      injector: {
        src: "injector/injector.js",
        dest: "injector/injector.min.js"
      }
    }
  })

  grunt.loadNpmTasks("grunt-wp-readme-to-markdown")
  grunt.loadNpmTasks("grunt-contrib-uglify")

  // Default task(s).
  grunt.registerTask("default", ["wp_readme_to_markdown", "uglify"])
}
