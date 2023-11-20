 <div class="row mt-3 justify-content-between" style="min-height: 512px">
     <div class="col-lg-12 px-4 mb-2">
         @if ($subscriber->email_bounce_id)
             <div class="col-12">
                 <h5><i class="fas fa-exclamation-circle text-danger" data-bs-toggle="tooltip" data-bs-placement="right"
                         title="{{ $subscriber->email_bounce_description }}"></i>
                     {{ $subscriber->email_bounce_description }}
                 </h5>
             </div>
         @endif
         <div id="payments">
             <table id="plan-table"
                 class="mt-3 xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                 style="width:100%">
                 <thead>
                     <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                         <th>Data do envio</th>
                         <th>Assunto</th>
                         <th>Status</th>
                     </tr>
                 </thead>
                 <tbody id="list-emails">

                 </tbody>
             </table>
         </div>
     </div>
 </div>

 <script>
     let email = "{{ $subscriber->email }}";

     let tableEmails = document.getElementById('list-emails');

     window.onload = function() {
         showStatusModal(true)
         axios.get(`/emails/list-email/${email}`)
             .then(function(response) {

                 let resp = response.data.emails;

                 if (resp.length > 0) {
                     for (let index = 0; index < resp.length; index++) {
                         const element = resp[index];
                         setEmailsInTable(
                             element.id,
                             element.subject,
                             element.send_date,
                             element.status,
                             element.messages_events,
                             index
                         );
                     }
                 }

                 showStatusModal(false)
                })
            .catch(function(error) {
                console.log(error);

                showStatusModal(false)
             });
     };

     function setEmailsInTable(id, subject, sendDate, status, messagesEvents, index) {
         let row = tableEmails.insertRow(index);

         row.id = `email-${id}`;

         let sendDateEmail = row.insertCell(0);

         let subjectEmail = row.insertCell(1);

         let statusEmail = row.insertCell(2);

         subjectEmail.innerHTML = subject;

         sendDateEmail.innerHTML = sendDate;

         statusEmail.innerHTML = `
        <div class="row">
            <div id="sent-${id}" data-toggle="tooltip" title="E-mail enviado em: &#013;${sendDate}"
            class="xgrow-fa-icons-emails-logs col-2">
                <i id="icon-sent-${id}" class="fas fa-paper-plane fa-lg" aria-hidden="true"></i>
            </div>
            <div id="received-${id}" data-toggle="tooltip" title="O e-mail ainda não foi recebido" class="xgrow-fa-icons-emails-logs col-2" style="background-color: #454954;">
                <i id="icon-received-${id}" class="fas fa fa-check fa-lg" style="color: #2A2E39;" aria-hidden="true"></i>
            </div>
            <div id="visualized-${id}" data-toggle="tooltip" title="O e-mail ainda não foi visualizado" class="xgrow-fa-icons-emails-logs col-2" style="background-color: #454954;">
                <i id="icon-visualized-${id}" class="fas fa-eye fa-lg" style="color: #2A2E39;" aria-hidden="true"></i>
            </div>
        </div>
        `;


         let sent = document.getElementById(`sent-${id}`);
         let iconSent = document.getElementById(`icon-sent-${id}`);

         let received = document.getElementById(`received-${id}`);
         let iconReceived = document.getElementById(`icon-received-${id}`);

         let visualized = document.getElementById(`visualized-${id}`);
         let iconVisualized = document.getElementById(`icon-visualized-${id}`);

         const delivered = checkMessagesEventsExists(messagesEvents, 'Delivered');
         const opened = checkMessagesEventsExists(messagesEvents, 'Opened');

         if (messagesEvents.length === 0) {
             sent.title = `E-mail não enviado`;
             sent.style.backgroundColor = "#454954";
             iconSent.style.color = "#2A2E39";

         } else if (messagesEvents.length > 1 && typeof delivered !== 'undefined' && typeof opened !== 'undefined') {

             received.title = `E-mail recebido em: ${messagesEvents[delivered].received_at}`;
             received.style.backgroundColor = "#93BC1E";
             iconReceived.style.color = "#FFFFFF";

             visualized.title = `E-mail visualizado em: ${messagesEvents[opened].received_at}`;
             visualized.style.backgroundColor = "#93BC1E";
             iconVisualized.style.color = "#FFFFFF";

         } else if (messagesEvents.length === 1 && typeof delivered !== 'undefined') {

             received.title = `E-mail recebido em: ${messagesEvents[delivered].received_at}`;
             received.style.backgroundColor = "#93BC1E";
             iconReceived.style.color = "#FFFFFF";
         }

     }

     function checkMessagesEventsExists(messageEventsArray, searchParameters) {
         for (let index = 0; index < messageEventsArray.length; index++) {
             const element = messageEventsArray[index];
             if (element.type === searchParameters) {
                 return index;
             }
         }
     }
 </script>
