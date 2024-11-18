export default function Sellers(props) {
    const sellers = props.sellers.map(seller => {
            return (
                <a href={'#'} key={seller.id} onClick={(e) => props.selectSeller(e, seller.id)}
                   className="dropdown-item ai-icon">
                    <span className="ml-2">{seller.name}</span>
                </a>
            )
        }
    );
    return (
        < div>
            {sellers}
        </div>
    )
}
