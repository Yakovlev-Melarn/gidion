import NavHeader from "./NavHeader";
import Header from "./Header";
import DezNav from "./DezNav";
import ContentBody from "./ContentBody";
import {useState} from "react";
import Index from "./Index/Index";

export function Token() {
    return document.querySelector(`meta[name="token"]`)
        .getAttribute('content');
}

export function AddLibrary(urlOfTheLibrary, async = false) {
    const script = document.createElement("script");
    script.src = urlOfTheLibrary;
    script.async = async;
    document.body.appendChild(script);
}

export default function App() {
    const [body] = useState(<Index/>)
    return (
        <div>
            <NavHeader/>
            <Header/>
            <DezNav/>
            <ContentBody body={body}/>
        </div>
    );
}
