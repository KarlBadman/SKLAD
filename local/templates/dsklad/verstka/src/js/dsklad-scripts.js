$(function () {
  "use strict";

  window.dsklad = {
    selectors: {
      headerMenuBtn : document.querySelector('.js-header-menu'),
      headerMenu : document.querySelector('.header-menu'),
      menuCloseBtn : document.querySelector('.js-menu-close'),
      html : document.querySelector('html'),
      body : document.querySelector('body'),
      header : document.querySelector('header'),
      headerSubmenuBtn : document.querySelector('.js-submenu'),
      submenuItems : document.querySelectorAll('.header-menu__item'),
      catalogBtn : document.querySelector('.catalog-btn'),
      hasSubmenus : document.querySelectorAll('.has-submenu'),

      footerMenu : document.querySelector('.js-footer-menu'),
      menuItems : document.querySelectorAll('.footer-menu__col'),

      headerSearchButtons : document.querySelectorAll('.js-search'),
      headerSearchCancel : document.querySelector('.js-search-cancel'),
      headerSearchMobileCancel : document.querySelector('.js-search-mobile-cancel'),
      headerSearchResult : document.querySelector('.header-search-result'),
      headerSearchInputs : document.querySelectorAll('.inp-search'),
      headerSearchClears : document.querySelectorAll('.js-search-clear'),

      miniCartDelItems: document.querySelectorAll('.mini-goods--cart .mini-goods__item'),
      miniCartFooter: document.querySelector('.ds-header-minicart__footer'),

      unpaidOrder : document.querySelector('.order-notification'),
      unpaidOrderClose : document.querySelector('.js-order-notification-close'),
    },

    jsHeaderMenu : {

      menuClose : function() {
        const headerMenu =  dsklad.selectors.headerMenu;
        const body = dsklad.selectors.body;

        headerMenu.classList.remove('opened');
        body.classList.remove('menu-opened');
      },

      headerMenuBtn : function() {
        const headerMenuBtn = dsklad.selectors.headerMenuBtn;
        const headerSearchResult = dsklad.selectors.headerSearchResult;
        const cleintWidth = document.body.clientWidth;

        headerMenuBtn.addEventListener('click', function () {
          const headerMenu =  dsklad.selectors.headerMenu;
          const body = dsklad.selectors.body;
          const submenuItems = dsklad.selectors.submenuItems;

          headerMenu.classList.toggle('opened');
          body.classList.toggle('menu-opened');

          if (headerSearchResult.classList.contains('opened')) {
            dsklad.jsHeaderSearch.searchClose();
          }

          if ( cleintWidth > 767) {
            for (var submenuItem of submenuItems) {
              if (submenuItem.classList.contains('active')) {
                submenuItem.classList.remove('active');
              } else {
                submenuItems[0].classList.add('active');
              }
            }
          }

        });
      },

      menuOptions : function() {
        const body = dsklad.selectors.body;
        const catalogBtn = dsklad.selectors.catalogBtn;

        if (body.classList.contains('menu-opened')) {
          catalogBtn.innerHTML = 'Закрыть';
        } else {
          catalogBtn.innerHTML = 'Каталог';
        }
      },

      menuCloseBtn : function() {
        const menuCloseBtn = dsklad.selectors.menuCloseBtn;
        const self = this;

        menuCloseBtn.addEventListener('click', function () {
          self.menuClose();
        });

      },

      headerSubmenu : function() {
        const html = dsklad.selectors.html;
        const submenuItems = dsklad.selectors.submenuItems;
        const headerSubmenuBtn = dsklad.selectors.headerSubmenuBtn;
        const hasSubmenus = dsklad.selectors.hasSubmenus;

        if ( !html.classList.contains('desktop') || (html.classList.contains('mobile') && html.classList.contains('landscape')) ) {
          for(var hasSubmenu of hasSubmenus) {
            hasSubmenu.addEventListener('click', function (event) {
              event.preventDefault();
            });
          }

          headerSubmenuBtn.addEventListener('click', function (event) {
            const target = event.target;
            const elem = target.closest('.header-menu__item');

            for(var submenuItem of submenuItems){
              if ( !elem.classList.contains('active') ) {
                submenuItem.classList.remove('active');
              }
            }

            if ( html.classList.contains('mobile') ) {
              elem.classList.toggle('active');
            } else {
              elem.classList.add('active');
            }
          });
        } else {
          headerSubmenuBtn.addEventListener('mouseover', function (event) {
            const target = event.target;
            const elem = target.closest('.has-submenu');

            if (!elem) return;

            for(var submenuItem of submenuItems){
              if ( !elem.parentElement.classList.contains('active') ) {
                submenuItem.classList.remove('active');
              }
            }

            elem.parentElement.classList.add('active');
          });
        }

      },

      init : function () {
        this.headerMenuBtn();
        this.menuOptions();
        this.menuCloseBtn();
        this.headerSubmenu();
      }
    },

    jsHeaderSearch : {
      searchOpen : function() {
        for(var headerSearchButton of dsklad.selectors.headerSearchButtons) {
          headerSearchButton.addEventListener('click', function () {
            dsklad.selectors.body.classList.add('search-opened');
            dsklad.selectors.headerSearchResult.classList.add('opened');
            dsklad.selectors.headerSearchCancel.classList.add('opened');
          });
        }
      },

      searchClose : function() {
        const self = this;
        self.searchClear();
        dsklad.selectors.body.classList.remove('search-opened');
        dsklad.selectors.headerSearchResult.classList.remove('opened');
        dsklad.selectors.headerSearchCancel.classList.remove('opened');
      },

      searchClear : function() {
        const clears = document.querySelectorAll('.js-search-clear');
        for (var clear of clears) {
          clear.previousSibling.value = '';
          clear.classList.remove('opened');
        }
      },

      searchCancel : function() {
        dsklad.selectors.headerSearchCancel.addEventListener('click', function () {
          dsklad.jsHeaderSearch.searchClose();
        });
      },

      searchMobileCancel : function() {
        const self = this;
        dsklad.selectors.headerSearchMobileCancel.addEventListener('click', function () {
          dsklad.selectors.body.classList.remove('search-opened');
          dsklad.selectors.headerSearchResult.classList.remove('opened');

          self.searchClear();
        });
      },

      addCancelBtn : function () {
        for (var headerSearchInput of dsklad.selectors.headerSearchInputs) {
          headerSearchInput.addEventListener('keyup', function () {
            const self = this;
            self.nextSibling.classList.add('opened');
          });
        }
      },

      cancelBtnClick : function () {
        const self = this;
        for (var headerSearchClear of dsklad.selectors.headerSearchClears) {
          headerSearchClear.addEventListener('click', function () {
            self.searchClear();
          });
        }
      },

      init : function () {
        this.searchOpen();
        this.searchCancel();
        this.searchMobileCancel();
        this.addCancelBtn();
        this.cancelBtnClick();
      },
    },

    jsBodyClick : {

      bodyClick : function () {

        document.addEventListener('click', function (event) {
          const target = event.target;
          const elem = target.closest('header');

          if (elem === null) {
            dsklad.jsHeaderMenu.menuClose();
            dsklad.jsHeaderSearch.searchClear();
            dsklad.jsHeaderSearch.searchClose();
          }

          dsklad.jsHeaderMenu.menuOptions();
        });
      },

      init : function() {
        this.bodyClick();
      }
    },

    jsFooterMenu : {
      footerMenu : function() {
        const footerMenu = dsklad.selectors.footerMenu;

        footerMenu.addEventListener('click', function(event){
          const target = event.target;
          const parent = target.parentNode;

          for(var menuItem of dsklad.selectors.menuItems){
            if (!parent.classList.contains('opened')) {
              menuItem.classList.remove('opened');
            }
          }

          parent.classList.toggle('opened');
        });
      },

      init : function () {
        this.footerMenu();
      },
    },

    jsMiniCartBorder : {
      addBorder : function() {
        const miniCartDelItems = dsklad.selectors.miniCartDelItems;
        const miniCartFooter = dsklad.selectors.miniCartFooter;
        let miniCartQuantity = miniCartDelItems.length;

        if (miniCartQuantity >= 5) {
          miniCartFooter.classList.add('border')
        } else {
          miniCartFooter.classList.remove('border')
        }
      },

      init: function () {
        this.addBorder();
      }
    },

    jsUnpaidOrder : {
      unpaidOrderClose : function () {
        const unpaidOrderClose = dsklad.selectors.unpaidOrderClose;
        const unpaidOrder = dsklad.selectors.unpaidOrder;

        unpaidOrderClose.addEventListener('click', function () {
          unpaidOrder.classList.add('hidden');
        })
      },

      init: function () {
        this.unpaidOrderClose();
      }
    },

    init: function() {
        this.jsHeaderMenu.init();
        this.jsHeaderSearch.init();
        this.jsFooterMenu.init();
        this.jsMiniCartBorder.init();
        this.jsBodyClick.init();
        this.jsUnpaidOrder.init();
    }
  };

  dsklad.init();
});
