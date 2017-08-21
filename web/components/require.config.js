var components = {
    "packages": [
        {
            "name": "justified-layout",
            "main": "justified-layout-built.js"
        },
        {
            "name": "materialize",
            "main": "materialize-built.js"
        }
    ],
    "baseUrl": "/components"
};
if (typeof require !== "undefined" && require.config) {
    require.config(components);
} else {
    var require = components;
}
if (typeof exports !== "undefined" && typeof module !== "undefined") {
    module.exports = components;
}