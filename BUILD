load("@rules_pkg//pkg:zip.bzl", "pkg_zip")
load("config.bzl", "VERSION", "JOOMLA_PATH")

# Set the version number

pkg_zip(
    name = "plg_admirorframes_%s" % VERSION,
    srcs = [
        ":files",
    ],
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
