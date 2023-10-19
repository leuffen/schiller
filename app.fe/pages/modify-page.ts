import {customElement, ka_session_storage, ka_sleep, KaCustomElement, KaHtmlElement, template} from "@kasimirjs/embed";
import {api_call, href, route, router} from "@kasimirjs/app";
import {currentRoute} from "@kasimirjs/app";
import {CurRoute} from "@kasimirjs/app";
import {API} from "../_routes";
import {DefaultModal, FlexModal} from "@kasimirjs/kit-bootstrap";

// language=html
let html = `
        
<div class="container-fluid" style="height: 50000px">
    <div class="row">
        <h2>Modify:</h2>
        
        <div class="mt-4">
            <h2>Generate Changelist from Text input instrcutions</h2>
            <textarea rows="15" style="width: 100%" ka.bind="$scope.instructions"></textarea>
            <button ka.on.click="$fn.aiGenerate($this)">AI Generate</button>
        </div>
        <div class="mt-4">
            <h2>Changelist</h2>
            <div ka.for="let change of changeList" class="row mb-3">
                <div class="col-3">[[change.subject]]</div>
                <div class="col-9">[[change.pid]]</div>
                <div class="col-12"><textarea style="width: 100%" ka.bind="change.description"></textarea></div>
                <div class="col-12"><button class="btn btn-secondary" ka.on.click="$fn.doChange($this, change.pid, change.description)">Do modify</button></div>
            </div>
        </div>
    </div>
</div>


`

@customElement()
@route("modify", "/static/modify")
@template(html)
class IndexPage extends KaCustomElement {

    constructor(public route : CurRoute) {
        super();
        let scope = this.init({
            instructions: "",
            changeList: [],
            $fn: {
                async initialize(templatePid: string, aliasPid: string = null) {
                    await api_call(API["api.initialize.post_POST"])
                },
                async aiGenerate(element: HTMLElement) {

                    element.innerHTML = "generating...";
                    scope.changeList = await api_call(API["api.ai.generate_change_request_POST"], {}, {instructions: scope.instructions});
                    element.innerHTML = "Modify";
                },
                async doChange(element: HTMLElement, pid: string, description: string) {
                    element.innerHTML = "modifying...";
                    await api_call(API["api.pid.modifyPageByInstructions_POST"], {pid: pid, lang: "de"}, {instructions: description});
                    element.innerHTML = "DONE";
                }
            }

        })
    }

    async connectedCallback(): Promise<void> {
        super.connectedCallback();



    }


    // language=html

}
