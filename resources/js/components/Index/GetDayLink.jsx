export default function GetDayLink(props) {
    if (props.day) {
        return (
            <a href="#" style={{marginRight: '1rem'}} onClick={(e) => {
                e.preventDefault()
                props.chart(props.seller, props.day)
            }}>{props.name}</a>
        )
    } else {
        return (
            <span style={{marginRight: '1rem', cursor:"not-allowed", color:"#4b4b4b"}}>{props.name}</span>
        )
    }
}
