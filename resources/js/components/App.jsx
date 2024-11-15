import NavHeader from "./NavHeader";
import Header from "./Header";
import {useEffect, useState} from "react";

export default function App() {
    const [error, setError] = useState(null);
    const [isLoaded, setIsLoaded] = useState(false);
    const [sellers, setSellers] = useState([]);
    function getToken(){
        return document.querySelector(`meta[name="token"]`)
            .getAttribute('content');
    }
    useEffect(() => {
        fetch("http://127.0.0.1:8000/api/getSellerList", {
            method: 'get',
            headers: new Headers({
                'authorization': getToken()
            })
        })
            .then(res => res.json())
            .then(
                (result) => {
                    setIsLoaded(true);
                    setSellers(result);
                },
                (error) => {
                    setIsLoaded(true);
                    setError(error);
                }
            )
    }, [])
    if (error) {
        return <div>Ошибка: {error.message}</div>;
    } else if (!isLoaded) {
        return <div>Загрузка...</div>;
    } else {
        return (
            <div>
                <NavHeader/>
                <Header/>
                <ul>
                    {sellers.map(item => (
                        <li key={item.id}>
                            {item.name} {item.price}
                        </li>
                    ))}
                </ul>
            </div>

        );
    }
}
