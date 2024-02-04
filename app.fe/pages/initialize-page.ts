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
        <h2>Initialize:</h2>
        <div>
            <button ka.on.click="$fn.initialize()">Initialize (Copy all struff from _root to /opt)</button>
        </div>
        <div class="mt-4">
            <h2>Ai Generate Data files</h2>
            <select ka.options="fileList" ka.bind="$scope.file"></select>
            <button ka.on.click="$fn.aiGenerate($this)">AI Generate</button>
        </div>
    </div>
</div>


`

@customElement("initialize-page")
@route("initialize", "/static/initialize")
@template(html)
class IndexPage extends KaCustomElement {

    constructor(public route : CurRoute) {
        super();
        let scope = this.init({
            fileList: ["/docs/_data/general.yml", "/.schiller-context.txt"],
            file : null,
            $fn: {
                async initialize(templatePid: string, aliasPid: string = null) {
                    await api_call(API["api.initialize.post_POST"])
                },
                async aiGenerate(element: HTMLElement) {
                    let sessionStorage = ka_session_storage({}, "initialize");

                    let modal = new FlexModal("Context einfügen", `<textarea style="width: 100%; min-height:400px" ka.bind="$scope.context"></textarea>`, [`<button ka.on.click="$fn.resolve()">Save</button>`]);
                    let result = (await modal.show({context: sessionStorage["__context"] ?? ""}))?.context;

                    if (!result)
                        return;

                    sessionStorage["__context"] = result;

                    element.innerHTML = "generating...";
                    await api_call(API["api.initialize.ai_gen_POST"], {file: scope.file}, {context: result});
                    element.innerHTML = "Modify";
                }
            }

        })
    }

    async connectedCallback(): Promise<void> {
        super.connectedCallback();



    }


    // language=html

}
