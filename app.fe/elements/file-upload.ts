import {customElement, KaCustomElement, KaHtmlElement, template} from "@kasimirjs/embed";
import {currentRoute, messageBus} from "@kasimirjs/app";
import {router} from "@kasimirjs/app";
import {UploadModal} from "./upload-modal";
import {IndexUpdatedMessage} from "../messages/index-updated-message";

// language=html
let html = `
<div>
    <input ka.ref="'upload1'" type="file" multiple>
    <div class="progress" ka.if="progress !== null">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-label="Animated striped example" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" ka.style.width="progress"></div>
    </div>
</div>`


@customElement("app-file-upload")
@template(html)
class FileUpload extends KaCustomElement {

    constructor() {
        super();

        let scope = this.init({
            progress: null
        });

    }

    doUpload() {

    }

    connectedCallback(): Promise<void> {
        super.connectedCallback();

        document.addEventListener("paste", async (e: ClipboardEvent) => {
            console.log(JSON.stringify(e.clipboardData.files[0]));
            let data = await (new UploadModal()).show();

            console.log("paste", e);
            let formData = new FormData();
            formData.append("file", e.clipboardData.files[0]);
        });

        this.scope.render();
        let scope = this.scope;
        scope.$ref.upload1.addEventListener("change", async () => {
            let files = scope.$ref.upload1.files;
            console.log(files);
            scope.progress = "5%";

            for (let index = 0; index < files.length; index++) {
                let file = files[index];
                let formData = new FormData();
                console.log(file);
                formData.append("file", file);

                scope.progress = Math.floor(Math.min(((index / files.length) * 100 + 4), 100)) + "%"

                let response = await fetch(`/v1/api/${router.currentRoute.route_params['subscription_id']}/${router.currentRoute.route_params['scope_id']}/upload`, {
                    method: "POST",
                    body: formData
                });
                let json = await response.json();
                if ( ! response.ok) {
                    alert("Upload failed: " + json.error?.message ?? "Undefined error");
                    console.error(json);
                }


                messageBus.trigger(new IndexUpdatedMessage())

            }
            scope.progress = null;
            scope.$ref.upload1.value = "";
        })
        return null
    }


}
