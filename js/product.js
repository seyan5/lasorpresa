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
    let filteredItems = li; // Keep track of currently displayed items
    let thisPage = 1;
    let limit = 8; // Number of items per page

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

                    // Filter products
                    filteredItems = li.filter(item => {
                        return (item.getAttribute('data-category') === displayItems || displayItems === "all");
                    });

                    // Reset to the first page when filtering
                    thisPage = 1;

                    // Load filtered items
                    loadItem();
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
            // Sort the filteredItems based on the selected option
            if (this.value === 'Default') {
                // Reset to the original filtered items
                filteredItems = [...originalItems].filter(item => {
                    return (item.getAttribute('data-category') === currentFilter || currentFilter === "all");
                });
            } else if (this.value === 'LowToHigh') {
                // Sort from lowest to highest
                filteredItems.sort((a, b) => {
                    const ax = Number(a.getAttribute('data-price'));
                    const bx = Number(b.getAttribute('data-price'));
                    return ax - bx;
                });
            } else if (this.value === 'HighToLow') {
                // Sort from highest to lowest
                filteredItems.sort((a, b) => {
                    const ax = Number(a.getAttribute('data-price'));
                    const bx = Number(b.getAttribute('data-price'));
                    return bx - ax;
                });
            }
        
            // Reset to the first page when sorting
            thisPage = 1;
        
            // Load sorted items
            loadItem();
        }
    }
    
    function loadItem() {
        let beginGet = limit * (thisPage - 1);
        let endGet = limit * thisPage - 1;
    
        // Hide all items first
        li.forEach(item => {
            item.style.display = 'none';
        });
    
        // Show filtered items based on pagination
        filteredItems.forEach((item, key) => {
            if (key >= beginGet && key <= endGet) {
                item.style.display = 'block';
            }
        });
    
        // Update pagination
        listPage(filteredItems.length);
    }

    function listPage(totalItems) {
        let count = Math.ceil(totalItems / limit);
 const listPageContainer = document.querySelector('.listPage');
        listPageContainer.innerHTML = '';

        if (thisPage != 1) {
            let prev = document.createElement('li');
            prev.innerText = 'PREV';
            prev.addEventListener('click', () => changePage(thisPage - 1));
            listPageContainer.appendChild(prev);
        }

        for (let i = 1; i <= count; i++) {
            let newPage = document.createElement('li');
            newPage.innerText = i;
            if (i == thisPage) newPage.classList.add('active');
            newPage.addEventListener('click', () => changePage(i));
            listPageContainer.appendChild(newPage);
        }

        if (thisPage != count) {
            let next = document.createElement('li');
            next.innerText = 'NEXT';
            next.addEventListener('click', () => changePage(thisPage + 1));
            listPageContainer.appendChild(next);
        }
    }

    function changePage(i) {
        if (i < 1 || i > Math.ceil(filteredItems.length / limit)) return; // Prevent out-of-bounds
        thisPage = i;
        loadItem();
    }

    // Initial load
    loadItem();
    new FilterProduct().run();
    new SortProduct().run();

})();

$(document).ready(function () {
    $('.indicator li').click(function () {
        var filter = $(this).attr('data-filter');
        $('.indicator li').removeClass('active');
        $(this).addClass('active');

        if (filter === 'all') {
            $('.item').show(); // Show all items
        } else {
            $('.item').hide(); // Hide all items
            $('.item[data-category="' + filter + '"]').show(); // Show filtered items
        }
    });
});