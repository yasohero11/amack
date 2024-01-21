class  ConfirmDialog {

    onShow = (...vars)=>{}

    constructor({text= "Are you sure?" , onConfirm = ()=>{} , onCancel,onShow = (...vars)=>{}}) {
        this.text =  text;

        this.onShow = onShow


        const dialogContainer = document.createElement('template');
        dialogContainer.innerHTML = `
   <dialog id="ConfirmDialog"  >
        <form  id="country-form">

            <div class="mb-3">
                <p>
                ${text}
                    </p>
            </div>
            <div class="mb-3">


            <footer>
                <menu style="padding: 0px">
                    <div class="ms-auto d-flex justify-content-end">
                        <button type="button" id="close" class="btn btn-sm btn-white mb-0 me-2" >
                            Close
                        </button>
                        <button type="button" id="confirm" class="btn btn-sm btn-dark btn-icon d-flex align-items-center mb-0">
                            <span class="btn-inner--text">Confirm</span>
                        </button>
                    </div>
                </menu>
            </footer>
        </form>
    </dialog>
`;

        const dialog = dialogContainer.content.cloneNode(true).firstElementChild;
         dialog.querySelector('#confirm').addEventListener("click" , function () {
             if (onConfirm)
                 onConfirm()

             this.closest('dialog').close('cancel')
         })

        dialog.querySelector('#close').addEventListener("click", function (){
            if(onCancel)
                onCancel()
            this.closest('dialog').close('cancel')
        })

        document.querySelector("main").appendChild(dialog);
    }

  show(...vars){
        this.onShow(vars)
        document.querySelector("#ConfirmDialog").showModal()
    }
}
