export default function ContentBody({...props}) {
    return (
        <div className="content-body">
            <div className="container-fluid" id="bodySection">{props.body}</div>
        </div>
    )
}
