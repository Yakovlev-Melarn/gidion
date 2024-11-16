import NavHeader from "./NavHeader";
import Header from "./Header";

export function token() {
    return document.querySelector(`meta[name="token"]`)
        .getAttribute('content');
}

export default function App() {
    return (
        <div>
            <NavHeader/>
            <Header/>
        </div>
    );
}
