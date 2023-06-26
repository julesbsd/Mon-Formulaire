document.addEventListener('DOMContentLoaded', function () {
    var select = document.getElementById('custom-select');
    var searchButton = document.getElementById('custom-search-button');

    searchButton.addEventListener('click', function () {
        var selectedOption = select.options[select.selectedIndex];
        var redirectURL = selectedOption.value;

        if (redirectURL) {
            window.open(redirectURL, '_blank');
        }
    });
});