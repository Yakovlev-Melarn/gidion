import RenderLineChart from "./RenderLineChart";
import GetDayLink from "./GetDayLink";


export default function Index(props) {
    return (
        <>
            <div className="row">
                <div className="col-xl-12 col-lg-12 col-sm-12" style={{height: '450px'}}>
                    <div className="card">
                        <div className="card-header">
                            <div className="col-md-9">
                                <h4 className="">Сводка на {props.selectedDay}</h4>
                            </div>
                            <div className="col-md-3">
                                <GetDayLink chart={props.chart} seller={props.seller} day={props.dates.prevDay}
                                            name="предыдущий день"/>
                                <GetDayLink chart={props.chart} seller={props.seller} day={props.dates.nextDay}
                                              name="следующий день"/>
                            </div>
                        </div>
                        <div className="card-body">
                            <RenderLineChart data={props.data}/>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
