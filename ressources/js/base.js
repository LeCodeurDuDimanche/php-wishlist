$(document).ready((e) => {
    console.log($(".wrapper"));
    $(".wrapper").each((i, o) => $(o).css("background-image", "url(" +$(o).data("background") + ")"));
});
