load("@rules_pkg//pkg:zip.bzl", "pkg_zip")
load("config.bzl", "VERSION", "JOOMLA_PATH")

pkg_zip(
    name = "build_release",
    srcs = [
        ":files",
    ],
    package_file_name = "plg_admirorframes_%s.zip" % VERSION,
    strip_prefix = ".",
)

filegroup(
    name = "files",
    srcs = [
        "_admirorframes.xml",
        "admirorframes.php",
        "admirorframes.scriptfile.php",
        "admirorframes.xml",
        "index.html",
    ] + glob([
        "admirorframes/**",
        "language/**",
    ]),
)
