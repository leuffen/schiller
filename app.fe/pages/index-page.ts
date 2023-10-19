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
        <h2>Pages: <a href="/static/initialize">Init</a> <a href="/static/modify">Modify</a></h2>
        <div ka.for="let page of pages" class="border-bottom p-3">
            <details ka.attr.open="openstate[page.pid] ?? false === true">
                <summary ka.on.click="openstate[page.pid] =  ! openstate[page.pid] ?? false">[[ page.pid ]]</summary>
                
            <div class="row">
                <div class="col">[[ page.pid ]]</div>
                <div class="col">
                    <label>Pid</label>
                    <input class="w-100" type="text" ka.bind="page.pid_new">
                </div>
                 <div class="col">
                    <label>Permalink</label>
                    <input class="w-100" type="text" ka.bind="page.permalink">
                </div>
                <div class="col">
                    <label>Title</label>
                    <input class="w-100" type="text" ka.bind="page.title">
                </div>
                <div class="col">
                    <label>Order</label>
                    <input class="w-100" type="number" ka.bind="page.order">
                </div>
                <div class="col">
                    <label>Image</label>
                    <input class="w-100" type="url" ka.bind="page.image">
                    <img ka.prop.src="page.image" style="max-height: 100px">
                </div>
                <div class="col">
                    <label>Description</label>
                    <input class="w-100" type="text" ka.bind="page.description">
                </div>
            </div>
            <div class="row mt-4">
                <div class="col">
                    <label>AI Instructions (no Markdown!):</label>
                    <textarea ka.bind="page._schiller_instructions" class="w-100"></textarea>
                </div>
                <div class="col">
                    <label>Copy from</label>
                    <select ka.ref="'copyFrom'+page.pid" ka.options="templates"></select>
                    <button ka.on.click="$fn.copyContent(page.pid, $scope.$ref['copyFrom'+page.pid].value)">Copy</button>
                </div>
                <div class="col">
                    <button ka.on.click="$fn.generate(page.pid, $this)">Generate Page</button>
                    <button ka.on.click="$fn.generateMeta(page.pid, $this)">Generate Meta Description</button>
                    <button ka.on.click="$fn.modify(page.pid, $this)">Modify</button>
                </div>
            </div>
</details>
        </div>
            
    </div>
    
    <div class="row bg-light p-4">
        <div class="col">
            
            <button ka.on.click="$fn.save()">Save</button>
        </div>
         <div class="col">
            <label>Create</label>
             <select ka.ref="'tplPid'" ka.options="templates" ka.ref="'tplPid'"></select>
            <input class="w-100" placeholder="Alias PID (optional)" type="text" ka.ref="'newPid'">
            <button ka.on.click="$fn.create($scope.$ref.tplPid.value, $scope.$ref.newPid.value)">Create</button>
             
        </div>
    </div>
</div>


`

@customElement("index-page")
@route("gallery", "/static")
@template(html)
class IndexPage extends KaCustomElement {

    constructor(public route : CurRoute) {
        super();
        let scope = this.init({
            pages: [],
            templates: [],
            openstate: {"pages/index1": "open"},
            $fn: {
                async create(templatePid: string, aliasPid: string = null) {
                    await api_call(API["api.pid.create_POST"], {templatePid, aliasPid})
                    await scope.$fn.update();
                },
                async save() {
                    console.log(scope.pages);
                    await api_call(API["api.pid.post_POST"], {}, scope.pages)
                    await scope.$fn.update();
                },
                async copyContent(pid: string, templatePid: string) {
                    await api_call(API["api.pid.copyContent_POST"], {pid, templatePid})
                    await scope.$fn.update();
                },
                async generate(pid: string, element: HTMLElement) {
                    element.innerHTML = "generating...";
                    await api_call(API["api.pid.generate_POST"], {pid});
                    element.innerHTML = "DONE";
                },
                async generateMeta(pid: string, element: HTMLElement) {
                    element.innerHTML = "generating...";
                    await api_call(API["api.pid.generateMeta_POST"], {pid});
                    element.innerHTML = "DONE";
                },
                async modify(pid: string, element: HTMLElement) {

                    let sessionStorage = ka_session_storage({}, "modify");

                    let modal = new FlexModal("Seite Ändern", `<textarea style="width: 100%; min-height:400px" ka.bind="$scope.text"></textarea>`, [`<button ka.on.click="$fn.resolve()">Save</button>`]);
                    let result = (await modal.show({text: sessionStorage[pid] ?? ""}))?.text;

                    if (!result)
                        return;

                    sessionStorage[pid] = result;

                    element.innerHTML = "generating...";
                    await api_call(API["api.pid.modifyPageByInstructions_POST"], {pid}, {instructions: result});
                    element.innerHTML = "Modify";
                },
                async update() : void {
                    scope.pages = [];

                    scope.pages = (await api_call(API["api.list_GET"])).pages;
                }
            }
        })
    }

    async connectedCallback(): Promise<void> {
        this.scope.templates = (await api_call(API["templates.list_GET"])).templates;

        super.connectedCallback();

        await this.scope.$fn.update();


    }


    // language=html

}
