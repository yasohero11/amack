


async function AFetch({url, method = "GET",body ,onResponse, onError = null, headers = {}}){
    try {
        headers["fetch"] = "true"
        headers["X-Requested-With"] = "XMLHttpRequest"
        const response = await fetch(url,{
            method:method,
            headers: headers,
            body:body,
        });

        if(response.ok) {
            console.log(`response is ok .................. with response ${response.status}`)
            console.log(await response.body)
            onResponse(response)
        }else{
            console.log(`failed with status ${response.status} ... `)
            if(onError) {
                onError(await response.json(), response.status === 422)
            }
            else{
                if(response.status === 422)
                    validateResponse(await response.json())
            }
        }
    } catch (errors) {
        if(onError)
            onError(errors)
    }
}


function validateResponse(errorsResponse){
        console.log("validation error")
        console.log(errorsResponse)
        for (const [key, value] of Object.entries(errorsResponse.errors)) {
            console.log(`${key}: ${value}`);
            const element =    document.querySelector(`.${key}-invalid-feedback`)
            element.innerText = value
            element.style.display = "block"
        }
}

function formDataToJson(formData){
    const formDataArray = Array.from(formData);
    return formDataArray.reduce((accumulator, [key, value]) => {
        accumulator[key] = value;
        return accumulator;
    }, {});
}

function hideInvalidMessages(){
    document.querySelectorAll(".invalid-feedback").forEach(e=>e.style.display = "none")
}

