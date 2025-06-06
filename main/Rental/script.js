<script>
  document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modal");
    const rentButtons = document.querySelectorAll(".featured-car-card .btn");

    rentButtons.forEach(function (button) {
      button.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default form submission

        const ownerName = button.dataset.ownerName;
        const mobileNumber = button.dataset.mobileNumber;

        displayModal(ownerName, mobileNumber);
      });
    });

    function displayModal(ownerName, mobileNumber) {
      const modalContent = modal.querySelector(".modal-content");
      const closeBtn = modal.querySelector(".close");
      const ownerNameInput = modal.querySelector("#ownerName");
      const mobileNumberInput = modal.querySelector("#mobileNumber");

      ownerNameInput.value = ownerName;
      mobileNumberInput.value = mobileNumber;

      modal.style.display = "block"; // Display the modal

      // Close the modal when the close button or outside the modal is clicked
      closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
      });

      window.addEventListener("click", function (event) {
        if (event.target === modal) {
          modal.style.display = "none";
        }
      });

      // Handle form submission
      const ownerDetailsForm = modal.querySelector("#ownerDetailsForm");
      ownerDetailsForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get form values
        const enteredOwnerName = ownerNameInput.value;
        const enteredMobileNumber = mobileNumberInput.value;

        // Here, you can perform further actions like sending the data to a server or displaying it somewhere
        console.log("Entered Owner Name:", enteredOwnerName);
        console.log("Entered Mobile Number:", enteredMobileNumber);

        // Close the modal after form submission
        modal.style.display = "none";
      });
    }
  });
</script>
