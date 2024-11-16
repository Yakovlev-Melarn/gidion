import {useState, useEffect} from 'react'
import {token} from "../App";

export default function Sellers(props) {
    const [sellers, setSellers] = useState([]);
    const [error, setError] = useState(false);
    const handleClick = (e) => {
        e.preventDefault();
        props.setSeller('Маша');
    }
    useEffect(() => {
        fetch("/api/getSellerList", {
            method: 'get',
            headers: new Headers({
                'authorization': token()
            })
        })
            .then(res => res.json())
            .then(
                (result) => {
                    setSellers(result);
                },
                (error) => {
                    setError(error);
                }
            )
    }, [])
    if (error) {
        console.log(error);
    } else {
        return (
            <div>
                {
                    sellers.map(seller => (
                        <a href={'#'} key={seller.id} onClick={handleClick} className="dropdown-item ai-icon">
                            <span className="ml-2">{seller.name}</span>
                        </a>
                    ))
                }
            </div>
        )
    }
}
