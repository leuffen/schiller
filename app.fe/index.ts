import {customElement, KaHtmlElement} from "@kasimirjs/embed";
import {route} from "@kasimirjs/app";
import "./elements/file-upload"
import "./pages/gallery-page"
import "./pages/index-page"
import "bootstrap"

@customElement("page-start")
@route("start", "/static")
class Start extends KaHtmlElement{

    html = `<div>Hello World</div>`

    connected(): Promise<void> {
        this.$tpl.render({});
        return Promise.resolve(undefined);
    }

    disconnected(): Promise<void> {
        return Promise.resolve(undefined);
    }
}

