import {KaModal} from "@kasimirjs/embed";


export class UploadModal extends KaModal {

    public async show() : Promise<string|null>{
        let promise = super.show();
        let scope = {
            value: "",
            $fn: {
                click: (e) => this.resolve(scope.value)
            }
        }
        this.render(scope);
        return promise;
    }

    // language=html
    html = `
    <modal>
        <input type="text" placeholder="Filename" ka.bind="$scope.value">
        <button ka.on.click="$fn.click()">Speichern</button>
    </modal>
    
    
    `

}
