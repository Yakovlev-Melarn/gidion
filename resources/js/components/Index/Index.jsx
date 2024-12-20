import RenderLineChart from "./RenderLineChart";


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
                                <a href="#" onClick={(e) => {
                                    e.preventDefault()
                                    props.updateChartData('prev')
                                }}>&laquo; Предыдущий день</a>
                                | <a href="#" onClick={(e) => {
                                e.preventDefault()
                                props.updateChartData('next')
                            }}>Следующий день &raquo;</a>
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
