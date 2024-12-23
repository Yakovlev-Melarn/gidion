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
            <div className="row">
                <div className="col-xl-3 col-xxl-4 col-lg-6 col-sm-6">
                    <div className="widget-stat card">
                        <div className="card-body p-4">
                            <div className="media">
									<span className="mr-3">
										<i className="flaticon-091-shopping-cart"/>
									</span>
                                <div className="media-body text-white text-right">
                                    <p className="mb-1">Заказано</p>
                                    <h3 className="text-white"><a href="#" className="tOrders">1000 ₽</a></h3>
                                    <p className="mb-0 fs-13">
                                        <span className="text-warning mr-1">3</span> шт.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
