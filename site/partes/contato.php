<section class="bg-whisper">
        <div class="container">
          <div class="row">
            <div class="col-md-10 col-lg-9 col-xl-7">
              <div class="section-50 section-md-75 section-xl-100">
                <h3>Consulta Gratuita</h3>
                <form class="rd-mailform" data-form-output="form-output-global" data-form-type="contact" method="post" action="bat/rd-mailform.php">
                  <div class="row row-30">
                    <div class="col-md-6">
                      <div class="form-wrap">
                        <input class="form-input" id="request-form-name" type="text" name="name" data-constraints="@Required">
                        <label class="form-label" for="request-form-name">Nome</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-wrap">
                        <input class="form-input" id="request-form-phone" type="text" name="phone" data-constraints="@Numeric @Required">
                        <label class="form-label" for="request-form-phone">Telefone</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-wrap">
                        <input class="form-input" id="request-form-email" type="email" name="email" data-constraints="@Email @Required">
                        <label class="form-label" for="request-form-email">E-mail</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-wrap form-wrap-outside">
                        <!--Select 2-->
                        <select class="form-input select-filter" id="request-form-select" data-minimum-results-for-search="Infinity">
                          <option>Direito de Família</option>
                          <option value="Family Law">Direito de Família</option>
                          <option value="Business Law">Direito Empresarial</option>
                          <option value="Civil Litigation">Litígio Civil</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-wrap">
                        <textarea class="form-input" id="feedback-2-message" name="message" data-constraints="@Required"></textarea>
                        <label class="form-label" for="feedback-2-message">Mensagem</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="row">
                        <div class="col-md-6">
                          <button class="button button-block button-primary" type="submit">Solicitar Consulta Gratuita</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <div class="col-xl-5 d-none d-xl-block">
              <div style="margin-top: -40px;"><img src="<?=assets_url()?>images/home-4-472x753.png" alt="" width="472" height="753"/>
              </div>
            </div>
          </div>
        </div>
      </section>