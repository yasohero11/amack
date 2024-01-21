class PopupAlert{

    constructor(properties){

        this.properties = properties == null ? {} : properties
        this.popupAlertDiv = document.createElement("div")
        this.popupAlertDiv.classList.add("popupAlertDiv")
        //this.properties.text = this.properties.text  == "" || this.properties.text == null? "Text Here" : this.properties.text
        this.popupAlertDiv.style.right = this.properties.xAxes  == null || !this.properties.xAxes  ? "2%" : this.properties.xAxes
        this.popupAlertDiv.style.bottom = this.properties.yAxes  == null || !this.properties.yAxes ? "2%" : this.properties.yAxes

        this.properties.showTime = this.properties.showTime == null || !this.properties.showTime ? 5000 : this.properties.shwoTime
        this.alertList = []
        document.body.appendChild(this.popupAlertDiv)
        this.popupAlertDiv.addEventListener("click",(e)=> this.removeAlrt(e.target))
    }

    showSuccessAlert(text) {
            // this.popupAlertDiv.insertAdjacentHTML("afterbegin" ,  `<div class="popup-alert">
            //                                                         <i class="far fa-check alert-icon"></i>
            //                                                         ${text == "" || text == null ? "Text Here" : text}
            //                                                         </div>`)

            let popupAlert = document.createElement("div")
            popupAlert.classList.add("popupAlert")
            popupAlert.insertAdjacentHTML("afterBegin" ,  "<i class='fas fa-check  alert-icon success-icon'></i>")
            popupAlert.appendChild(document.createTextNode(text == "" || text == null ? "Text Here" : text))

            let timeOut =  setTimeout(()=> this.removeAlrt(popupAlert)
                ,this.properties.showTime)
            this.popupAlertDiv.appendChild(popupAlert)
            this.alertList.push({
                element : popupAlert,
                timeOut,
            })
            console.log(this.alertList)

    }

    showFailureAlert(text) {
        // this.popupAlertDiv.insertAdjacentHTML("afterbegin" ,  `<div class="popup-alert">
        //                                                         <i class="fas fa-times "></i>
        //                                                         ${text == "" || text == null ? "Text Here" : text}
        //                                                         </div>`)

        let popupAlert = document.createElement("div")
        popupAlert.classList.add("popupAlert")
        popupAlert.insertAdjacentHTML("afterBegin" ,"<i class='fas fa-times  alert-icon failure-icon'></i>")
        popupAlert.appendChild(document.createTextNode(text == "" || text == null ? "Text Here" : text))
        let timeOut =  setTimeout(()=> this.removeAlrt(popupAlert)
                ,this.properties.showTime)
        this.popupAlertDiv.appendChild(popupAlert)
        this.alertList.push({
            element : popupAlert,
            timeOut,
        })

    }
    showLoadingAlert(text) {


        let popupAlert = document.createElement("div")
        popupAlert.classList.add("popupAlert")
        popupAlert.insertAdjacentHTML("afterBegin" ,`<div class="spinner-border text-info" role="status">
        <span class="sr-only ">Loading...</span>
      </div>`)
        popupAlert.appendChild(document.createTextNode(text == "" || text == null ? "Text Here" : text))

        this.popupAlertDiv.appendChild(popupAlert)
        this.alertList.push({
            element : popupAlert,
        })
        return popupAlert

    }
    removeAlrt(popupAlert){
                let indx = this.alertList.findIndex(item => item.element === popupAlert);
                console.log(indx)
                clearTimeout(this.alertList[indx].timeOut)
                this.alertList.splice(indx, indx >= 0 ? 1 : 0);
                popupAlert.remove()
                console.log(this.alertList)
    }

}
