export default function CustomTooltip({payload, label, active}) {
    if (active) {
        return (
            <div style={{
                backgroundColor: "rgba(0, 0, 0, 0.5)",
                boxShadow: "0 0 4px #0000002b",
                padding: "8px 12px",
                fontSize: "0.8rem",
                color: "#fff"
            }}>
                <p>{`Время: ${label}`}</p>
                <p><span style={{
                    backgroundColor: "#8884d8",
                    display: "inline-block",
                    width: "10px",
                    height: "10px",
                    marginRight: "5px"
                }}/>
                    Заказано на сумму: {payload[0].value}</p>
                <p><span style={{
                    backgroundColor: "#82ca9d",
                    display: "inline-block",
                    width: "10px",
                    height: "10px",
                    marginRight: "5px"
                }}/>
                    Выкуплено на сумму: {payload[1].value}</p>
            </div>
        );
    }
    return null;
}
