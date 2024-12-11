import {AddLibrary} from "../App";

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
                            <canvas id="lineChart_3"/>
                        </div>
                    </div>
                </div>
            </div>
            {AddLibrary('/vendor/chart.js/Chart.bundle.min.js')}
            {AddLibrary('/js/plugins-init/chartjs-init.js')}
        </>
    )
}
