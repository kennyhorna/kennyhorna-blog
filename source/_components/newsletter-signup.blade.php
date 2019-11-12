<div class="flex justify-center my-12 p-6 md:px-12 border border-gray-400 text-sm md:rounded-lg shadow mc_background">
  <!-- Begin Mailchimp Signup Form -->
  <div class="mc_embed_signup">
    <form
        action="https://gmail.us20.list-manage.com/subscribe/post?u=debad11f81ac71ce66fb76850&amp;id=5ff0ec32ee"
        method="POST"
        id="mc-embedded-subscribe-form"
        name="mc-embedded-subscribe-form"
        class="validate mc-embedded-subscribe-form newsletter__form"
        target="_blank"
        novalidate
    >
      <div class="flex flex-col items-center justify-center">
        <h2 class="text-2xl text-center my-2">¿Te agradan los artículos que publico?</h2>
        <div class="w-full md:w-3/4">
          <p>
            ¡Genial! Suscríbete. Solo recibirás un email mensual con el resumen de lo publicado.
            <u>Nada</u> de spam, lo prometo. Me fastidian tanto como a ti 😉.
          </p>
          <p>
            Puedes darte de baja en cualquier momento 🤚&#127995;
          </p>
        </div>
        <div class="w-full max-w-sm sm:w-2/5 my-4 flex flex-col sm:flex-row justify-center items-start">
          <div class="mc-field-group w-full sm:flex-1 flex flex-col mb-2 sm:mb-0 ">
            <input
                type="email"
                value=""
                name="EMAIL"
                class="required email rounded-full sm:rounded-r-none bg-white py-2 px-4 placeholder-gray-500 text-center sm:text-left text-gray-800 outline-none border border-white focus:border-purple-400" id="mce-EMAIL" placeholder="Email"
            >
          </div>
          <div class="w-full max-w-sm sm:w-auto rounded-full sm:rounded-l-none bg-purple-500 border border-purple-500 py-2 px-6 text-white cursor-pointer text-center">
            <input type="submit" value="Únete" name="subscribe" id="mc-embedded-subscribe" class="button bg-purple-500  cursor-pointer">
          </div>
        </div>

        <div id="mce-responses" class="clear">
          <div class="response" id="mce-error-response" style="display:none"></div>
          <div class="response" id="mce-success-response" style="display:none"></div>
        </div>
      </div>
    </form>
  </div>
<!--End Mailchimp Signup Form -->
</div>

@push('scripts')
<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
<script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[2]='LNAME';ftypes[2]='text';fnames[3]='ADDRESS';ftypes[3]='address';fnames[4]='PHONE';ftypes[4]='phone';fnames[5]='BIRTHDAY';ftypes[5]='birthday'; /*
 * Translated default messages for the $ validation plugin.
 * Locale: ES
 */
    $.extend($.validator.messages, {
      required: "Este campo es obligatorio.",
      remote: "Por favor, rellena este campo.",
      email: "Por favor, escribe una dirección de correo válida",
    });}(jQuery));var $mcj = jQuery.noConflict(true);</script>
<!--End mc_embed_signup-->
@endpush
