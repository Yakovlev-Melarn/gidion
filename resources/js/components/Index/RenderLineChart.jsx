import {Area, AreaChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis} from "recharts";
import CustomTooltip from "./CustomTooltip";

export default function RenderLineChart({data}) {
    return (
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
}
