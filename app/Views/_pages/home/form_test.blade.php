<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NthAjax Form Example</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <form
    data-nthajax="formdata"
    data-nthajax-success="Form submitted successfully!"
    data-nthajax-error="An error occurred while submitting the form."
    data-nthajax-submit="true"
    data-nthajax-waiting-caption="Submitting..."
    action="{{ cfg('app.base_url') }}/echo-request"
    method="POST">
    
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>
    <br><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br><br>
    <button type="submit">Submit</button>
  </form>

  <script>
    const NthAjax = (function () {
      let config = {
        popMessage: (message) => Swal.fire("Info", message, "info"),
        popError: (error) => Swal.fire("Error", error, "error"),
        popSuccess: (success) => Swal.fire("Success", success, "success"),
      };

      function setDefaults(newConfig) {
        config = { ...config, ...newConfig };
      }

      function handleFormSubmit(event) {
        event.preventDefault();

        const form = event.target;
        const submitButton = form.querySelector("[type=submit]");
        const originalText = submitButton ? submitButton.textContent : null;
        const waitingCaption = form.dataset.nthajaxWaitingCaption || "Submitting...";
        const disableOnSubmit = form.dataset.nthajaxSubmit !== "false";
        const keepDisabled = form.dataset.nthajaxSubmit === "true";
        const successHandler = form.dataset.nthajaxSuccess || config.popSuccess;
        const errorHandler = form.dataset.nthajaxError || config.popError;
        const dataFormat = form.dataset.nthajax || "formdata";

        if (submitButton && disableOnSubmit) {
          submitButton.disabled = true;
          submitButton.textContent = waitingCaption;
        }

        let body;
        if (dataFormat === "json") {
          const formData = new FormData(form);
          body = JSON.stringify(Object.fromEntries(formData.entries()));
        } else {
          body = new FormData(form);
        }

        fetch(form.action || window.location.href, {
          method: form.method || "POST",
          headers: dataFormat === "json" ? { "Content-Type": "application/json" } : {},
          body,
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then((data) => {
            if (typeof successHandler === "string") {
              config.popSuccess(successHandler);
            } else if (typeof successHandler === "function") {
              successHandler(data);
            }
            form.reset();
          })
          .catch((error) => {
            if (typeof errorHandler === "string") {
              config.popError(errorHandler);
            } else if (typeof errorHandler === "function") {
              errorHandler(error);
            }
          })
          .finally(() => {
            if (submitButton && (!keepDisabled || !disableOnSubmit)) {
              submitButton.disabled = false;
              submitButton.textContent = originalText;
            }
          });
      }

      function init(selector, customConfig = {}) {
        setDefaults(customConfig);

        const forms = document.querySelectorAll(selector);
        forms.forEach((form) => {
          form.addEventListener("submit", handleFormSubmit);
        });
      }

      return {
        init,
        setDefaults,
      };
    })();

    // Initialize NthAjax
    NthAjax.init("form[data-nthajax]", {
      popSuccess: (message) => Swal.fire("Success", message, "success"),
      popError: (error) => Swal.fire("Error", error, "error"),
    });
  </script>
</body>
</html>
