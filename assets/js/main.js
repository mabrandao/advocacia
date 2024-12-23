/**
* Template Name: NiceAdmin
* Updated: May 30 2023 with Bootstrap v5.3.0
* Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/
(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Sidebar toggle
   */
  if (select('.toggle-sidebar-btn')) {
    on('click', '.toggle-sidebar-btn', function(e) {
      select('body').classList.toggle('toggle-sidebar')
    })
  }

  /**
   * Search bar toggle
   */
  if (select('.search-bar-toggle')) {
    on('click', '.search-bar-toggle', function(e) {
      select('.search-bar').classList.toggle('search-bar-show')
    })
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Initiate tooltips
   */
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })

  /**
   * Initiate quill editors
   */
  if (select('.quill-editor-default')) {
    new Quill('.quill-editor-default', {
      theme: 'snow'
    });
  }

  if (select('.quill-editor-bubble')) {
    new Quill('.quill-editor-bubble', {
      theme: 'bubble'
    });
  }

  if (select('.quill-editor-full')) {
    new Quill(".quill-editor-full", {
      modules: {
        toolbar: [
          [{
            font: []
          }, {
            size: []
          }],
          ["bold", "italic", "underline", "strike"],
          [{
              color: []
            },
            {
              background: []
            }
          ],
          [{
              script: "super"
            },
            {
              script: "sub"
            }
          ],
          [{
              list: "ordered"
            },
            {
              list: "bullet"
            },
            {
              indent: "-1"
            },
            {
              indent: "+1"
            }
          ],
          ["direction", {
            align: []
          }],
          ["link", "image", "video"],
          ["clean"]
        ]
      },
      theme: "snow"
    });
  }

  /**
   * Initiate TinyMCE Editor
   */
  const initTinyMCE = () => {
    let base_url = "http://localhost/advocacia/";
    const useTinyMCE = document.querySelector('.tinymce-editor');
    if (useTinyMCE && typeof tinymce !== 'undefined') {
      tinymce.init({
        selector: '.tinymce-editor',
        language: 'pt_BR',
        height: 500,
        menubar: true,
        plugins: [
          'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
          'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
          'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
          'bold italic forecolor | alignleft aligncenter ' +
          'alignright alignjustify | bullist numlist outdent indent | ' +
          'removeformat | image | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        file_picker_callback: function(callback, value, meta) {
          let finalURL = base_url + 'assets/vendor/filemanager/filemanager/dialog.php?type=2&editor=tinymce&field_id=' + meta.fieldname + '&akey=jgvfghge4egh5e5heg';
          if (meta.filetype == 'image') {
            finalURL = base_url + 'assets/vendor/filemanager/filemanager/dialog.php?type=1&editor=tinymce&field_id=' + meta.fieldname + '&akey=jgvfghge4egh5e5heg';
          }
          tinymce.activeEditor.windowManager.openUrl({
            url: finalURL,
            title: 'Gerenciador de Arquivos',
            width: window.innerWidth * 0.8,
            height: window.innerHeight * 0.8,
            onMessage: function(api, message) {
              callback(message.content);
            }
          });
        },
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        automatic_uploads: true,
        images_upload_url: base_url + 'assets/vendor/filemanager/filemanager/upload.php',
        images_upload_base_path: base_url + 'assets/img/upload',
        images_reuse_filename: true
      });
    }
  };

  window.addEventListener('load', initTinyMCE);

  /**
   * Initiate Bootstrap validation check
   */
  var needsValidation = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(needsValidation)
    .forEach(function(form) {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })


  /**
   * Autoresize echart charts
   */
  const mainContainer = select('#main');
  if (mainContainer) {
    setTimeout(() => {
      new ResizeObserver(function() {
        select('.echart', true).forEach(getEchart => {
          echarts.getInstanceByDom(getEchart).resize();
        })
      }).observe(mainContainer);
    }, 200);
  }

  /**
   * Tornando a função openFileManager global
   */
  window.openFileManager = () => {
    let base_url = "http://localhost/advocacia/";
    const modalHtml = `
      <div class="modal fade" id="fileManagerModal" tabindex="-1" aria-labelledby="fileManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="fileManagerModalLabel">Gerenciador de Arquivos</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
              <iframe src="${base_url}assets/vendor/filemanager/filemanager/dialog.php?type=0&editor=custom&akey=jgvfghge4egh5e5heg" 
                      style="width: 100%; height: 80vh; border: none;"></iframe>
            </div>
          </div>
        </div>
      </div>
    `;

    // Adiciona o modal ao body se ainda não existir
    if (!document.getElementById('fileManagerModal')) {
      document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    // Abre o modal usando o Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('fileManagerModal'));
    modal.show();
  };

  /**
   * Adiciona o botão de gerenciar arquivos na navbar ou como botão flutuante
   */
  const addFileManagerButton = () => {
    // Procura o menu de navegação principal
    const mainNav = document.querySelector('#navbar');
    let buttonAdded = false;

    if (mainNav) {
      // Procura primeiro por uma ul existente
      let ul = mainNav.querySelector('ul.navbar-nav');
      
      // Se não encontrar uma ul, cria uma nova
      if (!ul) {
        ul = document.createElement('ul');
        ul.className = 'navbar-nav ms-auto';
        mainNav.appendChild(ul);
      }

      // Verifica se o botão já existe
      if (!document.querySelector('#fileManagerBtn')) {
        const li = document.createElement('li');
        li.className = 'nav-item';
        li.innerHTML = `
          <button id="fileManagerBtn" class="btn btn-outline-primary mx-2" onclick="openFileManager()">
            <i class="bi bi-folder"></i> Gerenciar Arquivos
          </button>
        `;
        ul.appendChild(li);
        buttonAdded = true;
      }
    }

    // Se não conseguiu adicionar na navbar, cria um botão flutuante
    if (!buttonAdded && !document.querySelector('#fileManagerFloatingBtn')) {
      const floatingBtn = document.createElement('div');
      floatingBtn.innerHTML = `
        <button id="fileManagerFloatingBtn" 
                class="btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle shadow" 
                style="width: 60px; height: 60px; z-index: 1050;"
                onclick="openFileManager()"
                title="Gerenciar Arquivos">
          <i class="bi bi-folder fs-4"></i>
        </button>
      `;
      document.body.appendChild(floatingBtn.firstElementChild);
    }
  };

  /**
   * Chama a função quando a página carregar e após um pequeno delay
   * para garantir que todos os elementos foram carregados
   */
  window.addEventListener('load', () => {
    setTimeout(addFileManagerButton, 500);
  });

})();