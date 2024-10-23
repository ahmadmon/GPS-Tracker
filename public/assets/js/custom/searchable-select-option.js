// Custom add search option
const selected = document.querySelector(".selected-box");
const optionsContainer = document.querySelector(".options-container");
const searchBox = document.querySelector(".search-box input");
const finalValue = document.getElementById('finalValue');
const optionsList = document.querySelectorAll(".selection-option");

selected.addEventListener("click", () => {
    optionsContainer.classList.toggle("active");

    searchBox.value = "";
    filterList("");

    if (optionsContainer.classList.contains("active")) {
        searchBox.focus();
    }
});

optionsList.forEach((o) => {
    o.addEventListener("click", (e) => {
        finalValue.value = e.target.children[0].value;
        selected.innerHTML = o.querySelector("label").innerHTML;
        optionsContainer.classList.remove("active");
    });
});

searchBox.addEventListener("keyup", function (e) {
    filterList(e.target.value);
});

const filterList = (searchTerm) => {
    searchTerm = searchTerm.toLowerCase();
    optionsList.forEach((option) => {
        let label =
            option.firstElementChild.nextElementSibling.innerText.toLowerCase();
        if (label.indexOf(searchTerm) !== -1) {
            option.style.display = "block";
        } else {
            option.style.display = "none";
        }
    });
};

// Ensure selected value stays on page load based on hidden input
const currentValue = finalValue.value;
if (currentValue) {
    const selectedOption = document.querySelector(`.selection-option input[value="${currentValue}"]`)
    if (selectedOption) {
        selected.innerHTML = selectedOption.nextElementSibling.innerText;
    }
}
