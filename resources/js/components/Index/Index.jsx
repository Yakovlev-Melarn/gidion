import {AreaChart, Area, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer} from 'recharts';

const data = [
    {
        "name": "00:00",
        "uv": 4000,
        "pv": 2400,
    },
    {
        "name": "01:00",
        "uv": 3000,
        "pv": 1398,
    },
    {
        "name": "02:00",
        "uv": 2000,
        "pv": 9800,
    },
    {
        "name": "03:00",
        "uv": 2780,
        "pv": 3908,
    },
    {
        "name": "04:00",
        "uv": 1890,
        "pv": 4800,
    },
    {
        "name": "05:00",
        "uv": 2390,
        "pv": 3800,
    },
    {
        "name": "06:00",
        "uv": 3490,
        "pv": 4300,
    },
    {
        "name": "07:00",
        "uv": 4000,
        "pv": 2400,
    },
    {
        "name": "08:00",
        "uv": 3000,
        "pv": 1398,
    },
    {
        "name": "09:00",
        "uv": 2000,
        "pv": 9800,
    },
    {
        "name": "10:00",
        "uv": 2780,
        "pv": 3908,
    },
    {
        "name": "11:00",
        "uv": 1890,
        "pv": 4800,
    },
    {
        "name": "12:00",
        "uv": 2390,
        "pv": 3800,
    },
    {
        "name": "13:00",
        "uv": 3490,
        "pv": 4300,
    }
];

function CustomTooltip({payload, label, active}) {
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

const renderLineChart = (
    <ResponsiveContainer width="100%" height={300}>
        <AreaChart data={data}
                   margin={{top: 10, right: 30, left: 0, bottom: 0}}>
            <defs>
                <linearGradient id="colorUv" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#8884d8" stopOpacity={0.8}/>
                    <stop offset="95%" stopColor="#8884d8" stopOpacity={0}/>
                </linearGradient>
                <linearGradient id="colorPv" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#82ca9d" stopOpacity={0.8}/>
                    <stop offset="95%" stopColor="#82ca9d" stopOpacity={0}/>
                </linearGradient>
            </defs>
            <XAxis stroke="#fff" style={{
                fontSize: '0.7rem'
            }} dataKey="name"/>
            <YAxis style={{
                fontSize: '0.7rem'
            }} stroke="#fff"/>
            <CartesianGrid stroke="#473F72" strokeDasharray="3 3"/>
            <Tooltip content={<CustomTooltip/>}/>
            <Area type="monotone" dataKey="uv" stroke="#8884d8" fillOpacity={1} fill="url(#colorUv)"/>
            <Area type="monotone" dataKey="pv" stroke="#82ca9d" fillOpacity={1} fill="url(#colorPv)"/>
        </AreaChart>
    </ResponsiveContainer>
);
export default function Index() {
    return (
        <>
            <div className="row">
                <div className="col-xl-12 col-lg-12 col-sm-12" style={{height: '450px'}}>
                    <div className="card">
                        <div className="card-header">
                            <input type="hidden" className="selectedDate" value="2024-12-11 00:00:00"/>
                            <div className="col-md-9">
                                <h4 className="">Сводка на ...?</h4>
                            </div>
                            <div className="col-md-3">
                                <a href="#">&laquo; Предыдущий день</a>
                                | <a href="#">Следующий день &raquo;</a>
                            </div>
                        </div>
                        <div className="card-body">
                            {renderLineChart}
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
