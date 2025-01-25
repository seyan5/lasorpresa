<!-- back button style -->
<style>
    .btn-secondary {
    position: fixed; /* Sticks the button to a fixed position */
    top: 4rem; /* Positions it 20px from the bottom of the screen */
    left: 27rem; /* Positions it 20px from the left of the screen */
    background-color: #333; /* Secondary button color */
    color: #ffffff; /* White text */
    border: none; /* Removes border */
    border-radius: 5px; /* Smooth corners */
    padding: 10px 20px; /* Adds padding for a comfortable size */
    font-size: 16px; /* Adjusts text size */
    cursor: pointer; /* Changes cursor to pointer on hover */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow */
    transition: background-color 0.3s ease; /* Smooth hover effect */
}

.btn-secondary:hover {
    background-color: #d6a98f; /* Slightly darker color on hover */
}
</style>

<button onclick="history.back()" class="btn btn-secondary">Back</button>