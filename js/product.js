let previewContainer = document.querySelector('.products-preview');
let previewBox = previewContainer.querySelectorAll('.preview');

document.querySelectorAll('.products-container .product').forEach(product => {
    product.onclick = () => {
        previewContainer.style.display = 'flex';
        let name = product.getAttribute('data-name');
        previewBox.forEach(preview => {
            let target = preview.getAttribute('data-target');
            if (name == target) {
                preview.classList.add('active');
            }
        });
    };
});

previewBox.forEach(close => {
    close.querySelector('.fa-times').onclick = () => {
        close.classList.remove('active');
        previewContainer.style.display = 'none';
    };
});

(function() {
    let field = document.querySelector('.products-container');
    let li = Array.from(field.children);

    // Function to filter products based on categories
    function FilterProduct() {
        for (let i of li) {
            const name = i.querySelector('h3').textContent.trim();
            i.setAttribute("data-category", name); // Set category based on product name

            const price = i.querySelector('.price').textContent.trim();
            const priceValue = Number(price.substring(1)); // Remove the dollar sign and convert to number
            i.setAttribute("data-price", priceValue);
        }

        let indicator = document.querySelector('.indicator').children;

        this.run = function() {
            for (let i = 0; i < indicator.length; i++) {
                indicator[i].onclick = function() {
                    for (let x = 0; x < indicator.length; x++) {
                        indicator[x].classList.remove('active');
                    }
                    this.classList.add('active');
                    const displayItems = this.getAttribute('data-filter');

                    for (let z = 0; z < li.length; z++) {
                        li[z].style.transform = "scale(0)";
                        setTimeout(() => {
                            li[z].style.display = "none";
                        }, 500);

                        if ((li[z].getAttribute('data-category') === displayItems) || displayItems === "all") {
                            li[z].style.transform = "scale(1)";
                            setTimeout(() => {
                                li[z].style.display = "block";
                            }, 500);
                        }
                    }
                };
            }
        };
    }

    // Function to sort products based on price
    function SortProduct() {
        let select = document.getElementById('select');

        this.run = () => {
            addEvent();
        };

        function addEvent() {
            select.onchange = sortingValue;
        }

        function sortingValue() {
            if (this.value === 'Default') {
                resetProducts();
            }
            if (this.value === 'LowToHigh') {
                sortProducts(true);
            }
            if (this.value === 'HighToLow') {
                sortProducts(false);
            }
        }

        function resetProducts() {
            while (field.firstChild) {
                field.removeChild(field.firstChild);
            }
            li.forEach(product => {
                field.appendChild(product);
            });
        }

        function sortProducts(asc) {
            let dm = asc ? 1 : -1;
            li.sort((a, b) => {
                const ax = Number(a.getAttribute('data-price'));
                const bx = Number(b.getAttribute('data-price'));
                return (ax - bx) * dm;
            });

            resetProducts();
            li.forEach(product => {
                field.appendChild(product);
            });
        }
    }

    // Initialize the filtering and sorting functionality
    new FilterProduct().run();
    new SortProduct().run();
})();