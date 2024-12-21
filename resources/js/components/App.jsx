import NavHeader from "./NavHeader";
import Header from "./Header";
import DezNav from "./DezNav";
import ContentBody from "./ContentBody";
import {useState, useEffect} from "react";
import Index from "./Index/Index";
import {convertDate} from "../libs/Gidion";

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
    const chartData = [{
        "name": "00:00",
        "uv": 0,
        "pv": 0
    }];
    const [sellers, setSellers] = useState([])
    const [error, setError] = useState(false);
    const [selectedSeller, setSelectedSeller] = useState(sellers)
    const [dates, setDates] = useState({selectedDay: null, nextDay: null, prevDay: null})
    const [body, setBody] = useState(<Index data={chartData} selectedDay={dates.selectedDay} dates={dates}/>)
    console.log('Render App')
    console.log('Seller: ' + selectedSeller.id)
    console.log(error)

    function getSellerList() {
        return fetch("/api/getSellerList", {
            method: 'get',
            headers: new Headers({
                'authorization': Token()
            })
        }).then(res => res.json())
            .then(
                (result) => {
                    setSellers(result);
                },
                (error) => {
                    setError(error);
                }
            )
    }

    function getSelectedSeller() {
        return fetch("/api/getSelectedSeller", {
            method: 'get',
            headers: new Headers({
                'authorization': Token()
            })
        }).then(res => res.json())
            .then(
                (result) => {
                    setSelectedSeller(result);
                    getChartData(result.id)
                },
                (error) => {
                    setError(error);
                }
            ).then()
    }

    function selectSeller(e, id) {
        e.preventDefault();
        if (id !== selectedSeller.id) {
            sellers.map(seller => {
                if (seller.id === id) {
                    setSelectedSeller(seller);
                }
            });
            fetch("/api/setSelectedSeller", {
                method: 'post',
                headers: new Headers({
                    'authorization': Token()
                }),
                body: JSON.stringify({
                    sellerId: id
                })
            }).then(res => res.json()).then(
                (result) => {
                    getChartData(result.id)
                }
            );
        }
    }

    function getChartData(sellerId, date = null) {
        if (!sellerId) {
            sellerId = selectedSeller.id
        }
        fetch("/api/getChartData", {
            method: 'post',
            headers: new Headers({
                'authorization': Token()
            }),
            body: JSON.stringify({
                sellerId: sellerId,
                date: date
            })
        }).then(res => res.json()).then(
            (result) => {
                let formattedDate = convertDate(new Date(result.meta.selectedDay))
                let newDates = {
                    selectedDay: result.meta.selectedDay,
                    nextDay: result.meta.nextDay,
                    prevDay: result.meta.prevDay
                }
                setDates(newDates);
                setBody(<Index data={result.data} selectedDay={formattedDate} dates={newDates} chart={getChartData}
                               seller={sellerId}/>)
            }
        );
    }

    useEffect(() => {
        let sellerList = getSellerList();
        sellerList.then(() => getSelectedSeller())
    }, []);
    return (
        <>
            <NavHeader/>
            <Header sellers={sellers} selectedSeller={selectedSeller} selectSeller={selectSeller}/>
            <DezNav/>
            <ContentBody body={body}/>
        </>
    );
}
