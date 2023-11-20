import { Queue, Worker } from 'bullmq'

//defines the Queue
const myQueue = new Queue('integrations', {connection: {}})

//adds 
async function addJobsDummy(){
    await myQueue.add('jobOne', {foo: 'bar'})
    await myQueue.add('jobOne', {qux: 'baz'})
}

new Worker('foo', async(job) => {});

(async()=>{await addJobsDummy()})().then(() => {console.log('added')})



