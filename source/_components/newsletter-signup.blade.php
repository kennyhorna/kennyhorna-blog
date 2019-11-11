<div class="flex justify-center my-12 p-6 md:px-12 bg-gray-200 border border-gray-400 text-sm md:rounded shadow">
  <!-- Begin Mailchimp Signup Form -->
  <div id="mc_embed_signup">
    <form action='https://gmail.us20.list-manage.com/subscribe/post?u=debad11f81ac71ce66fb76850&amp;id=5ff0ec32ee'
          method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"
          target="_blank" novalidate>
      <div id="mc_embed_signup_scroll">
        <h2>¿Te interesan los temas que publico?</h2>
        <p>
          Genial, te recomendaría subscribirte. Solo recibirás <b>un email mensual</b> con el resumen de lo publicado.
          <u>Nada</u> de <i>spam</i>, lo detesto.
        </p>
        <p>
          Puedes darte de baja en cualquier momento 🤚&#127995;!
        </p>
        <div class="mc-field-group">
          <label for="mce-EMAIL border border-2 ">Email </label>
          <input type="email" value="" name="EMAIL" class="required email " id="mce-EMAIL" placeholder="Email"
                 oninvalid="this.setCustomValidity('El e-mail es requerido.')">
        </div>
        <div style="position: absolute; left: -5000px;" aria-hidden="true" class="flex flex-col md:block">
          <input type="text" name="b_25582686a9fc051afd5453557_189578c854" tabindex="-1" value="">
        </div>
        <div class="clear">
          <input type="submit" value="Únete" name="subscribe" id="mc-embedded-subscribe" class="button">
        </div>
      </div>
      <div id="mce-responses" class="clear">
        <div class="response" id="mce-error-response" style="display:none"></div>
        <div class="response" id="mce-success-response" style="display:none"></div>
      </div>
      <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    </form>
  </div>
  <!--End Mailchimp Signup Form -->
</div>

@push('scripts')
  <script src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
  <script>(function ($) {
      window.fnames = new Array();
      window.ftypes = new Array();
      fnames[0] = 'EMAIL';
      ftypes[0] = 'email';
      fnames[1] = 'FNAME';
      ftypes[1] = 'text';
      fnames[2] = 'LNAME';
      ftypes[2] = 'text';
    }(jQuery));
    var $mcj = jQuery.noConflict(true);
  </script>
@endpush
