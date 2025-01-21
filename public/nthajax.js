// ajaxifyForm 
// requires SweetAlert 2
// (by) Nabeel Ali (mail2nabeelali@gmail.com)


function ajaxifyForm(formEl, successCallback, beforeCallBack = null, keepButtonDisabledOnSuccess = false) {
  formEl.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (beforeCallBack) {
      let res = beforeCallBack();
      if (res === false) {
        return;
      }
    }

    const submitButton = formEl.querySelector('button[type="submit"]');
    const submitButtonText = submitButton.innerHTML;
    // console.log(submitButton.innerHTML);
    submitButton.disabled = true;
    submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;


    const formData = new FormData(formEl);
    const url = formEl.getAttribute('action');
    const method = formEl.getAttribute('method');

    try {
      const response = await fetch(url, {
        headers: {
          'X-REQUESTED-WITH': 'XMLHttpRequest'
        },
        method: method,
        body: formData,
      });


      const json = await response.json();

      let message = '<ul style="text-align: left; list-style-type: none;">';
      let lines = json?.message?.split("\n") || "";
      for (let i = 0; i < lines.length; i++) {
        message += '<li>' + lines[i] + '</li>';
      }
      message += '</ul>';
      json.message = message;

      // console.log(json);
      if (json.success === true) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          html: json.message || 'Operation completed successfully.',
        }).then(() => {
          if (typeof successCallback === 'function') {
            successCallback(formEl, json);
          }
          if (!keepButtonDisabledOnSuccess) {
            submitButton.disabled = false;
            submitButton.innerHTML = submitButtonText;
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          html: json.message || 'An error occurred while processing your request.',
        }).then(() => {
          submitButton.disabled = false;
          submitButton.innerHTML = submitButtonText;
        });
      }
    } catch (error) {
      console.error(error);
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: 'An error occurred while processing your request.',
      }).then(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = submitButtonText;
      });
    }
  });
}


async function ajaxifyAction(url, method, data, submitButton, successCallback, keepButtonDisabledOnSuccess = true) {

  const submitButtonText = submitButton.innerHTML;
  console.log(submitButton.innerHTML);
  submitButton.disabled = true;
  submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

  try {
    const response = await fetch(url, {
      headers: {
        'X-REQUESTED-WITH': 'XMLHttpRequest'
      },
      method: method,
      body: data,
    });


    const json = await response.json();

    let message = '<ul style="text-align: left; list-style-type: none;">';
    let lines = json?.message?.split("\n") || "";
    for (let i = 0; i < lines.length; i++) {
      message += '<li>' + lines[i] + '</li>';
    }
    message += '</ul>';
    json.message = message;

    console.log(json);
    if (json.success === true) {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        html: json.message || 'Operation completed successfully.',
      }).then(() => {
        if (typeof successCallback === 'function') {
          successCallback(json);
        }
        if (!keepButtonDisabledOnSuccess) {
          submitButton.disabled = false;
          submitButton.innerHTML = submitButtonText;
        }
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        html: json.message || 'An error occurred while processing your request.',
      }).then(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = submitButtonText;
      });
    }
  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      html: 'An error occurred while processing your request.',
    }).then(() => {
      submitButton.disabled = false;
      submitButton.innerHTML = submitButtonText;
    });
  }
}




function ajaxify() {
  const forms = document.querySelectorAll('form[data-ajaxify]');

  forms.forEach((formEl) => {
    const beforeCallbackName = formEl.getAttribute('data-before');
    const successCallbackName = formEl.getAttribute('data-success');
    const keepButtonDisabled = (formEl.getAttribute('data-keep-disabled') ?? 'false') === 'true';

    const beforeCallback = beforeCallbackName 
      ? (typeof window[beforeCallbackName] === 'function' 
          ? window[beforeCallbackName] 
          : eval(beforeCallbackName)) 
      : null;

    const successCallback = successCallbackName 
      ? (typeof window[successCallbackName] === 'function' 
          ? window[successCallbackName] 
          : eval(successCallbackName)) 
      : null;

      console.log(successCallback)

    ajaxifyForm(formEl, successCallback, beforeCallback, keepButtonDisabled);
  });
}

document.addEventListener('DOMContentLoaded', ajaxify);
