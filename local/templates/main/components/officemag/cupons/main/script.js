BX.ready(function () {
  let cuponsPage = {

    /**
     *  Point of entry.
     */
    start : function ()
    {
      cuponsPage.getCupon();
      cuponsPage.checkCupon();
    },
    /**
     * Send request, to get cupon.
     */
    getCupon : function ()
    {
      BX.bind(BX('js-getCupon'), 'click', function() {
        let elCupon = BX('js-showCupon');
        let elDiscount = BX('js-showDiscount');
        elCupon.classList.add("hide")
        elDiscount.classList.add("hide")
        BX.ajax.runComponentAction(
          'officemag:cupons',
          'getCupon',
          {
            mode: 'class',
          }).then(response => {
            if(response.status == 'success')
            {
              return response.data;
            }
        }).then(data => {

          elCupon.textContent = data.code;
          elCupon.classList.remove("hide");

          elDiscount.textContent = data.discount;
          elDiscount.classList.remove("hide");
        })

      });
    },
    /**
     *  Send request, to check code.
     */
    checkCupon : function (){
      BX.bind(BX('js-checkCupon'), 'submit', function(e) {
        e.preventDefault();
        let statusCupon = BX('js-statusCupon');
        statusCupon.classList.add("hide")
        const formData = new FormData(e.srcElement);
        const bxFormData = new BX.ajax.FormData();
        for(let [name, value] of formData)
        {
          bxFormData.append(name, value);
        }

        BX.ajax.runComponentAction(
          'officemag:cupons',
          'checkCupon',
          {
            mode: 'class',
            data: {
              post: bxFormData.elements
            },
          }).then(response => {
          if(response.status == 'success')
          {
            return response.data;
          }
        }).then(data => {
          console.log(data)
          statusCupon.classList.remove("hide")
          statusCupon.textContent = data.discount;

        })
      });
    }
  }
  cuponsPage.start();
});
