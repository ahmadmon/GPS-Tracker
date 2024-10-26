document.querySelectorAll('.select-box').forEach(selectBox => {
    const selected = selectBox.querySelector(".selected-box");
    const optionsContainer = selectBox.querySelector(".options-container");
    const searchBox = selectBox.querySelector(".search-box input");
    const finalValue = selectBox.querySelector("input[type=hidden]");
    const optionsList = selectBox.querySelectorAll(".selection-option");

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
            const inputElement = o.querySelector("input.radio"); // به‌جای children[0]، مستقیماً input را انتخاب می‌کنیم
            if (inputElement) {
                finalValue.value = inputElement.value;
                selected.innerHTML = o.querySelector("label").innerHTML;
                optionsContainer.classList.remove("active");
            }
        });
    });

    searchBox.addEventListener("keyup", function (e) {
        filterList(e.target.value);
    });

    const filterList = (searchTerm) => {
        searchTerm = searchTerm.toLowerCase();
        optionsList.forEach((option) => {
            let label = option.querySelector("label").innerText.toLowerCase();
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
        const selectedOption = selectBox.querySelector(`.selection-option input[value="${currentValue}"]`);
        if (selectedOption) {
            selected.innerHTML = selectedOption.nextElementSibling.innerText;
        }
    }
});
